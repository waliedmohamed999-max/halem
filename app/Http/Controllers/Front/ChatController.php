<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Support\ChatAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(private readonly ChatAssistantService $assistant)
    {
    }

    public function index(Request $request)
    {
        $conversation = $this->currentConversation($request);

        if ($conversation) {
            $conversation->markSeenByCustomer();
            $conversation->load(['messages' => fn ($query) => $query->latest('id')->limit(40)]);
        }

        return view('front.chat', [
            'conversation' => $conversation,
            'messages' => $conversation ? $conversation->messages->sortBy('id')->values() : collect(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $locale = app()->getLocale();
        $conversation = $this->currentConversation($request);

        $rules = [
            'message' => ['nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt'],
        ];

        if (! $conversation) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:50'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        $hasMessage = filled($data['message'] ?? null);
        $hasAttachment = $request->hasFile('attachment');

        if ($hasAttachment && ! $request->file('attachment')?->isValid()) {
            throw ValidationException::withMessages([
                'attachment' => $locale === 'ar'
                    ? 'تعذر رفع الملف. أعد المحاولة بملف صالح.'
                    : 'The attachment upload failed. Please try again with a valid file.',
            ]);
        }

        if (! $hasMessage && ! $hasAttachment) {
            return response()->json([
                'message' => $locale === 'ar' ? 'اكتب رسالة أو أرفق ملفًا.' : 'Write a message or attach a file.',
            ], 422);
        }

        if (! $conversation) {
            $conversation = ChatConversation::query()->create([
                'public_token' => (string) Str::uuid(),
                'visitor_name' => $data['name'],
                'visitor_phone' => $data['phone'],
                'visitor_email' => $data['email'] ?? null,
                'status' => 'bot',
                'ai_enabled' => true,
                'human_requested' => false,
            ]);

            $request->session()->put('front_chat_conversation_id', $conversation->id);
        }

        $attachmentData = $this->storeAttachment($request, 'customer');

        $conversation->messages()->create([
            'sender_type' => 'customer',
            'message' => (string) ($data['message'] ?? ''),
            ...$attachmentData,
        ]);

        $conversation->forceFill([
            'admin_unread_count' => $conversation->admin_unread_count + 1,
            'last_message_at' => now(),
            'last_message_preview' => Str::limit($this->previewText((string) ($data['message'] ?? ''), $attachmentData['attachment_name'] ?? null, $locale), 140),
        ])->save();

        if ($conversation->status !== 'closed') {
            if ($this->assistant->shouldHandoff((string) ($data['message'] ?? ''), $locale)) {
                $conversation->forceFill([
                    'status' => 'human',
                    'human_requested' => true,
                    'ai_enabled' => false,
                    'last_message_at' => now(),
                ])->save();

                $aiReply = $this->assistant->handoffReply($locale);
                $conversation->messages()->create([
                    'sender_type' => 'system',
                    'message' => $aiReply,
                    'meta' => ['handoff' => true],
                ]);

                $conversation->forceFill([
                    'customer_unread_count' => $conversation->customer_unread_count + 1,
                    'last_message_preview' => Str::limit($aiReply, 140),
                ])->save();
            } elseif ($conversation->ai_enabled && $conversation->status === 'bot') {
                $aiReply = $this->assistant->generateReply($conversation, $locale);
                $conversation->messages()->create([
                    'sender_type' => 'ai',
                    'message' => $aiReply,
                ]);

                $conversation->forceFill([
                    'customer_unread_count' => $conversation->customer_unread_count + 1,
                    'last_message_at' => now(),
                    'last_message_preview' => Str::limit($aiReply, 140),
                ])->save();
            }
        }

        $conversation->refresh();

        return response()->json([
            'conversation' => $this->conversationPayload($conversation),
            'messages' => $this->messagesPayload($conversation),
            'last_message_id' => $conversation->messages()->max('id'),
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $conversation = $this->currentConversation($request);
        if (! $conversation) {
            return response()->json(['conversation' => null, 'messages' => []]);
        }

        $afterId = (int) $request->input('after_id', 0);
        $messages = $conversation->messages()
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();

        $conversation->markSeenByCustomer();

        return response()->json([
            'conversation' => $this->conversationPayload($conversation->fresh()),
            'messages' => $this->messagesPayloadFromCollection($messages),
            'last_message_id' => (int) ($conversation->messages()->max('id') ?? 0),
        ]);
    }

    public function stream(Request $request): StreamedResponse
    {
        $conversation = $this->currentConversation($request);
        $lastId = (int) $request->input('last_id', 0);

        return response()->stream(function () use ($conversation, $lastId): void {
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');

            if (! $conversation) {
                echo "event: ping\n";
                echo 'data: ' . json_encode(['ok' => true]) . "\n\n";
                @ob_flush();
                flush();

                return;
            }

            $startedAt = time();
            while (! connection_aborted() && (time() - $startedAt) < 30) {
                $fresh = $conversation->fresh();
                $messages = $fresh->messages()
                    ->where('id', '>', $lastId)
                    ->orderBy('id')
                    ->get();

                foreach ($messages as $message) {
                    echo "event: chat-message\n";
                    echo 'data: ' . json_encode($this->messagePayload($message), JSON_UNESCAPED_UNICODE) . "\n\n";
                    $lastId = $message->id;
                }

                echo "event: conversation-state\n";
                echo 'data: ' . json_encode($this->conversationPayload($fresh), JSON_UNESCAPED_UNICODE) . "\n\n";
                echo "event: ping\n";
                echo 'data: {"ok":true}' . "\n\n";
                @ob_flush();
                flush();
                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function currentConversation(Request $request): ?ChatConversation
    {
        $conversationId = $request->session()->get('front_chat_conversation_id');

        if (! $conversationId) {
            return null;
        }

        return ChatConversation::query()->find($conversationId);
    }

    private function conversationPayload(ChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'status' => $conversation->status,
            'visitor_name' => $conversation->visitor_name,
            'visitor_phone' => $conversation->visitor_phone,
            'customer_unread_count' => $conversation->customer_unread_count,
            'admin_unread_count' => $conversation->admin_unread_count,
        ];
    }

    private function messagesPayload(ChatConversation $conversation): array
    {
        return $this->messagesPayloadFromCollection(
            $conversation->messages()->orderBy('id')->get()
        );
    }

    private function messagesPayloadFromCollection($messages): array
    {
        return $messages->map(fn (ChatMessage $message) => $this->messagePayload($message))->values()->all();
    }

    private function messagePayload(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'message' => $message->message,
            'created_at' => optional($message->created_at)->format('Y-m-d H:i:s'),
            'attachment_url' => $message->attachment_url,
            'attachment_name' => $message->attachment_name,
            'attachment_mime' => $message->attachment_mime,
        ];
    }

    private function storeAttachment(Request $request, string $directory): array
    {
        if (! $request->hasFile('attachment')) {
            return [];
        }

        $file = $request->file('attachment');
        if (! $file || ! $file->isValid()) {
            return [];
        }

        $path = $file->store("chat/{$directory}", 'public');
        $originalName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_BASENAME);
        $originalName = trim(preg_replace('/[\r\n\t]+/', ' ', $originalName) ?? '');
        $originalName = Str::limit($originalName, 120, '');

        return [
            'attachment_path' => $path,
            'attachment_name' => $originalName !== '' ? $originalName : basename($path),
            'attachment_mime' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function previewText(string $message, ?string $attachmentName, string $locale): string
    {
        if (trim($message) !== '') {
            return $message;
        }

        if ($attachmentName) {
            return $locale === 'ar'
                ? 'مرفق: ' . $attachmentName
                : 'Attachment: ' . $attachmentName;
        }

        return '';
    }
}
