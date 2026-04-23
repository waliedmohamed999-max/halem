@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .chat-admin-shell { display:grid; grid-template-columns: minmax(0,1.55fr) 320px; gap:1rem; }
    .chat-admin-card { border:1px solid rgba(24,74,64,.12); border-radius:1.2rem; background:#fff; box-shadow:0 14px 34px rgba(18,59,53,.06); overflow:hidden; }
    .chat-admin-head { padding:1rem 1.15rem; border-bottom:1px solid rgba(24,74,64,.08); display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; background:linear-gradient(120deg, rgba(29,143,120,.07), rgba(223,244,238,.76)); }
    .chat-admin-thread { min-height:520px; max-height:68vh; overflow:auto; display:flex; flex-direction:column; gap:.85rem; padding:1rem; background:linear-gradient(180deg,#f8fbff,#eef6ff); }
    .chat-admin-bubble { max-width:78%; padding:.82rem .95rem; border-radius:1rem; line-height:1.7; box-shadow:0 8px 18px rgba(15,32,54,.05); }
    .chat-admin-bubble small { display:block; margin-top:.35rem; opacity:.72; font-size:.73rem; }
    .bubble-customer { align-self:flex-start; background:#fff; color:#16324d; border:1px solid #dfe8f2; border-bottom-left-radius:.35rem; }
    .bubble-admin { align-self:flex-end; background:#1d7dfa; color:#fff; border-bottom-right-radius:.35rem; }
    .bubble-ai { align-self:flex-start; background:#e8f6ee; color:#0e5c3e; border:1px solid #cde8d9; border-bottom-left-radius:.35rem; }
    .bubble-system { align-self:center; background:#fff5d8; color:#7b5a00; border:1px solid #f1e2ab; max-width:90%; }
    .chat-admin-body { padding:1rem 1.15rem; }
    .chat-side-box { border:1px dashed rgba(24,74,64,.14); border-radius:1rem; background:#f8fcfb; padding:.9rem; margin-bottom:.85rem; }
    .chat-admin-attachment { display:inline-flex; align-items:center; gap:.35rem; margin-top:.55rem; padding:.35rem .6rem; border-radius:.7rem; text-decoration:none; font-size:.82rem; background:rgba(255,255,255,.18); color:inherit; }
    .bubble-customer .chat-admin-attachment,
    .bubble-ai .chat-admin-attachment,
    .bubble-system .chat-admin-attachment { background:#f2f7fb; color:#18425a; }
    @media (max-width: 991.98px) { .chat-admin-shell { grid-template-columns:1fr; } }
</style>

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'محادثة العميل' : 'Customer Conversation' }}</h4>
            <p class="admin-list-subtitle">{{ $conversation->visitor_name }} - {{ $conversation->visitor_phone }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-secondary" href="{{ route('admin.messages.index', app()->getLocale()) }}">{{ $isAr ? 'العودة للمحادثات' : 'Back to Conversations' }}</a>
        </div>
    </div>

    <div class="chat-admin-shell">
        <div class="chat-admin-card">
            <div class="chat-admin-head">
                <div>
                    <strong>{{ $isAr ? 'سجل المحادثة' : 'Conversation Log' }}</strong>
                    <div class="text-secondary small">{{ $isAr ? 'يتم تحديث المحادثة تلقائيًا' : 'Conversation auto-refreshes' }}</div>
                </div>
                <span class="admin-status-pill" id="admin-chat-status">{{ $conversation->status }}</span>
            </div>
            <div class="chat-admin-thread" id="admin-chat-thread">
                @foreach($conversation->messages as $message)
                    <div class="chat-admin-bubble bubble-{{ $message->sender_type }}">
                        <div style="white-space: pre-wrap;">{{ $message->message ?: ($isAr ? 'مرفق بدون نص.' : 'Attachment without text.') }}</div>
                        @if($message->attachment_url)
                            <a class="chat-admin-attachment" href="{{ $message->attachment_url }}" target="_blank">
                                {{ $message->attachment_name ?: ($isAr ? 'فتح المرفق' : 'Open attachment') }}
                            </a>
                        @endif
                        <small>
                            {{ $message->created_at?->format('Y-m-d H:i') }}
                            @if($message->sender_type === 'admin' && $message->user)
                                - {{ $message->user->name }}
                            @endif
                        </small>
                    </div>
                @endforeach
            </div>
            <div class="chat-admin-body">
                <form method="POST" action="{{ route('admin.messages.update', [app()->getLocale(), $conversation]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'وضع المحادثة' : 'Conversation Mode' }}</label>
                            <select class="form-select" name="status">
                                <option value="bot" @selected($conversation->status === 'bot')>{{ $isAr ? 'البوت' : 'Bot' }}</option>
                                <option value="human" @selected($conversation->status === 'human')>{{ $isAr ? 'بشري' : 'Human' }}</option>
                                <option value="closed" @selected($conversation->status === 'closed')>{{ $isAr ? 'مغلقة' : 'Closed' }}</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{ $isAr ? 'رد خدمة العملاء' : 'Customer Service Reply' }}</label>
                            <textarea class="form-control" rows="3" name="reply" placeholder="{{ $isAr ? 'اكتب ردك هنا وسيظهر مباشرة للعميل' : 'Write your reply here and it will appear to the customer immediately' }}"></textarea>
                            <div class="mt-2">
                                <input class="form-control" type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn btn-primary">{{ $isAr ? 'حفظ وإرسال الرد' : 'Save and Send Reply' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="chat-admin-card">
            <div class="chat-admin-head">
                <strong>{{ $isAr ? 'بيانات المحادثة' : 'Conversation Details' }}</strong>
            </div>
            <div class="chat-admin-body">
                <div class="chat-side-box">
                    <div class="small text-secondary">{{ $isAr ? 'الاسم' : 'Name' }}</div>
                    <strong>{{ $conversation->visitor_name }}</strong>
                </div>
                <div class="chat-side-box">
                    <div class="small text-secondary">{{ $isAr ? 'الهاتف' : 'Phone' }}</div>
                    <strong>{{ $conversation->visitor_phone }}</strong>
                </div>
                <div class="chat-side-box">
                    <div class="small text-secondary">{{ $isAr ? 'البريد' : 'Email' }}</div>
                    <strong>{{ $conversation->visitor_email ?: '-' }}</strong>
                </div>
                <div class="chat-side-box">
                    <div class="small text-secondary">{{ $isAr ? 'آخر رسالة' : 'Last Message' }}</div>
                    <strong>{{ $conversation->last_message_at?->format('Y-m-d H:i') ?: '-' }}</strong>
                </div>
                <div class="chat-side-box">
                    <div class="small text-secondary">{{ $isAr ? 'محوّل لبشري' : 'Human Requested' }}</div>
                    <strong>{{ $conversation->human_requested ? ($isAr ? 'نعم' : 'Yes') : ($isAr ? 'لا' : 'No') }}</strong>
                </div>
                @if($conversation->assignedUser)
                    <div class="chat-side-box">
                        <div class="small text-secondary">{{ $isAr ? 'المسؤول' : 'Assigned To' }}</div>
                        <strong>{{ $conversation->assignedUser->name }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const thread = document.getElementById('admin-chat-thread');
    const status = document.getElementById('admin-chat-status');
    const feedUrl = @json(route('admin.messages.feed', [app()->getLocale(), $conversation]));
    const streamUrl = @json(route('admin.messages.stream', [app()->getLocale(), $conversation]));
    let lastMessageId = {{ (int) ($conversation->messages->max('id') ?? 0) }};
    let stream = null;

    const appendMessage = (message) => {
        const item = document.createElement('div');
        item.className = `chat-admin-bubble bubble-${message.sender_type}`;
        const body = document.createElement('div');
        body.textContent = message.message || '{{ $isAr ? 'مرفق بدون نص.' : 'Attachment without text.' }}';
        body.style.whiteSpace = 'pre-wrap';
        const meta = document.createElement('small');
        meta.textContent = `${message.created_at ?? ''}${message.user_name ? ' - ' + message.user_name : ''}`;
        item.appendChild(body);
        if (message.attachment_url) {
            const attachment = document.createElement('a');
            attachment.className = 'chat-admin-attachment';
            attachment.href = message.attachment_url;
            attachment.target = '_blank';
            attachment.textContent = message.attachment_name || '{{ $isAr ? 'فتح المرفق' : 'Open attachment' }}';
            item.appendChild(attachment);
        }
        item.appendChild(meta);
        thread.appendChild(item);
        thread.scrollTop = thread.scrollHeight;
    };

    const startStream = () => {
        if (stream) {
            stream.close();
        }
        stream = new EventSource(`${streamUrl}?last_id=${lastMessageId}`);
        stream.addEventListener('chat-message', (event) => {
            const message = JSON.parse(event.data);
            appendMessage(message);
            lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
        });
        stream.addEventListener('conversation-state', (event) => {
            const conversation = JSON.parse(event.data);
            status.textContent = conversation.status;
        });
        stream.onerror = async () => {
            stream.close();
            try {
                const response = await fetch(`${feedUrl}?after_id=${lastMessageId}`, { headers: { 'Accept': 'application/json' } });
                if (response.ok) {
                    const payload = await response.json();
                    status.textContent = payload.conversation.status;
                    (payload.messages || []).forEach((message) => {
                        appendMessage(message);
                        lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
                    });
                }
            } catch (error) {
            }
            setTimeout(startStream, 3000);
        };
    };

    startStream();
});
</script>
@endsection
