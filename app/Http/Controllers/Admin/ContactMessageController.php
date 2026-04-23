<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatConversation::query()->latest('last_message_at')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($term): void {
                $builder->where('visitor_name', 'like', "%{$term}%")
                    ->orWhere('visitor_phone', 'like', "%{$term}%")
                    ->orWhere('visitor_email', 'like', "%{$term}%");
            });
        }

        $conversations = $query->paginate(20)->withQueryString();

        return view('admin.messages.index', compact('conversations'));
    }

    public function show(ChatConversation $message)
    {
        $message->load(['messages.user', 'assignedUser']);
        $message->markSeenByAdmin();

        return view('admin.messages.show', ['conversation' => $message]);
    }

    public function update(Request $request, ChatConversation $message)
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:bot,human,closed'],
            'reply' => ['nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt'],
        ]);

        $hasReply = filled($data['reply'] ?? null);
        $hasAttachment = $request->hasFile('attachment');

        if ($hasReply || $hasAttachment) {
            $reply = trim((string) $data['reply']);
            $attachmentData = $this->storeAttachment($request);
            $message->messages()->create([
                'sender_type' => 'admin',
                'user_id' => auth()->id(),
                'message' => $reply,
                ...$attachmentData,
            ]);

            $message->forceFill([
                'status' => $data['status'] ?? 'human',
                'assigned_user_id' => auth()->id(),
                'customer_unread_count' => $message->customer_unread_count + 1,
                'last_message_at' => now(),
                'last_message_preview' => Str::limit($this->previewText($reply, $attachmentData['attachment_name'] ?? null), 140),
            ])->save();
        } elseif (isset($data['status'])) {
            $message->update([
                'status' => $data['status'],
                'ai_enabled' => $data['status'] === 'bot',
                'human_requested' => $data['status'] === 'human',
            ]);
        }

        return back()->with('success', 'Conversation updated successfully');
    }

    public function destroy(ChatConversation $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index', app()->getLocale())->with('success', 'Conversation deleted successfully');
    }

    public function feed(ChatConversation $message, Request $request): JsonResponse
    {
        $afterId = (int) $request->input('after_id', 0);
        $items = $message->messages()
            ->with('user')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();

        $message->markSeenByAdmin();

        return response()->json([
            'conversation' => [
                'id' => $message->id,
                'status' => $message->status,
                'human_requested' => $message->human_requested,
                'admin_unread_count' => $message->admin_unread_count,
                'customer_unread_count' => $message->customer_unread_count,
            ],
            'messages' => $items->map(fn (ChatMessage $item) => [
                'id' => $item->id,
                'sender_type' => $item->sender_type,
                'message' => $item->message,
                'created_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
                'user_name' => $item->user?->name,
            ])->values(),
            'last_message_id' => (int) ($message->messages()->max('id') ?? 0),
        ]);
    }

    public function stream(ChatConversation $message, Request $request): StreamedResponse
    {
        $lastId = (int) $request->input('last_id', 0);

        return response()->stream(function () use ($message, $lastId): void {
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');

            $startedAt = time();
            while (! connection_aborted() && (time() - $startedAt) < 30) {
                $fresh = $message->fresh();
                $items = $fresh->messages()
                    ->with('user')
                    ->where('id', '>', $lastId)
                    ->orderBy('id')
                    ->get();

                foreach ($items as $item) {
                    echo "event: chat-message\n";
                    echo 'data: ' . json_encode([
                        'id' => $item->id,
                        'sender_type' => $item->sender_type,
                        'message' => $item->message,
                        'created_at' => optional($item->created_at)->format('Y-m-d H:i:s'),
                        'user_name' => $item->user?->name,
                        'attachment_url' => $item->attachment_url,
                        'attachment_name' => $item->attachment_name,
                    ], JSON_UNESCAPED_UNICODE) . "\n\n";
                    $lastId = $item->id;
                }

                echo "event: conversation-state\n";
                echo 'data: ' . json_encode([
                    'id' => $fresh->id,
                    'status' => $fresh->status,
                    'human_requested' => $fresh->human_requested,
                    'admin_unread_count' => $fresh->admin_unread_count,
                    'customer_unread_count' => $fresh->customer_unread_count,
                ], JSON_UNESCAPED_UNICODE) . "\n\n";
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

    private function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [];
        }

        $file = $request->file('attachment');
        $path = $file->store('chat/admin', 'public');

        return [
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_mime' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function previewText(string $message, ?string $attachmentName): string
    {
        if ($message !== '') {
            return $message;
        }

        return $attachmentName ? 'Attachment: ' . $attachmentName : '';
    }
}
