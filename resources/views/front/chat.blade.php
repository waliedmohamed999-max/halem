@extends('layouts.front')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .chat-shell { display:grid; gap:1rem; }
    .chat-hero, .chat-card { border:1px solid #d7e5f3; border-radius:1.2rem; background:#fff; box-shadow:0 14px 34px rgba(17,53,94,.06); }
    .chat-hero { padding:1.15rem 1.2rem; background:linear-gradient(140deg,#edf7ff,#ffffff); }
    .chat-hero h2 { margin:0; color:#123a6a; font-weight:800; }
    .chat-hero p { margin:.4rem 0 0; color:#5d7186; }
    .chat-layout { display:grid; grid-template-columns:minmax(0,1.4fr) 320px; gap:1rem; }
    .chat-card { overflow:hidden; }
    .chat-card-head { padding:1rem 1.15rem; border-bottom:1px solid #e6eef6; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .chat-card-body { padding:1rem 1.15rem; }
    .chat-thread { min-height:460px; max-height:62vh; overflow:auto; display:flex; flex-direction:column; gap:.85rem; background:linear-gradient(180deg,#f8fbff,#eff7ff); border-radius:1rem; padding:1rem; border:1px solid #e1ebf5; }
    .chat-bubble { max-width:min(78%, 680px); padding:.8rem .95rem; border-radius:1rem; line-height:1.7; box-shadow:0 10px 22px rgba(12,46,84,.05); }
    .chat-bubble small { display:block; margin-top:.35rem; opacity:.7; font-size:.74rem; }
    .chat-customer { align-self:flex-end; background:#1d7dfa; color:#fff; border-bottom-right-radius:.3rem; }
    .chat-admin, .chat-ai, .chat-system { align-self:flex-start; border-bottom-left-radius:.3rem; }
    .chat-admin { background:#fff; color:#17324d; border:1px solid #dce7f2; }
    .chat-ai { background:#e8f6ee; color:#0e5c3e; border:1px solid #cde8d9; }
    .chat-system { background:#fff5d8; color:#7b5a00; border:1px solid #f1e2ab; }
    .chat-form-grid { display:grid; gap:.75rem; }
    .chat-side-card { border:1px dashed #d4e1ee; border-radius:1rem; padding:.9rem; background:#f8fbff; }
    .chat-side-card strong { color:#123a6a; display:block; margin-bottom:.4rem; }
    .chat-status-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .75rem; border-radius:999px; font-size:.82rem; font-weight:700; }
    .chat-status-pill.bot { background:#e7f5ee; color:#17704f; }
    .chat-status-pill.human { background:#eaf2ff; color:#2258a8; }
    .chat-status-pill.closed { background:#f3f4f6; color:#556070; }
    .chat-attachment { display:inline-flex; align-items:center; gap:.4rem; margin-top:.55rem; padding:.35rem .6rem; border-radius:.7rem; background:rgba(255,255,255,.18); color:inherit; text-decoration:none; font-size:.82rem; }
    .chat-attachment.light { background:#f2f7fb; color:#18425a; }
    @media (max-width: 991.98px) { .chat-layout { grid-template-columns:1fr; } .chat-thread { max-height:none; } }
</style>

<div class="chat-shell">
    <div class="chat-hero">
        <h2>{{ $isAr ? 'الدعم المباشر' : 'Live Support' }}</h2>
        <p>{{ $isAr ? 'ابدأ المحادثة مع المساعد الذكي، وسيتم تحويلك مباشرة لخدمة العملاء عند الطلب.' : 'Start with the AI assistant and get transferred directly to customer service when needed.' }}</p>
    </div>

    <div class="chat-layout">
        <div class="chat-card">
            <div class="chat-card-head">
                <div>
                    <strong>{{ $isAr ? 'المحادثة' : 'Conversation' }}</strong>
                    <div class="text-secondary small">{{ $isAr ? 'تحديث تلقائي كل بضع ثوان' : 'Auto-refresh every few seconds' }}</div>
                </div>
                <span class="chat-status-pill {{ $conversation?->status ?? 'bot' }}" id="chat-status-pill">
                    {{ $conversation?->status === 'human' ? ($isAr ? 'خدمة العملاء' : 'Human Support') : ($conversation?->status === 'closed' ? ($isAr ? 'مغلقة' : 'Closed') : ($isAr ? 'AI مساعد' : 'AI Assistant')) }}
                </span>
            </div>
            <div class="chat-card-body">
                <div class="chat-thread" id="chat-thread">
                    @forelse($messages as $message)
                        <div class="chat-bubble chat-{{ $message->sender_type }}">
                            <div style="white-space: pre-wrap;">{{ $message->message ?: ($isAr ? 'مرفق بدون نص.' : 'Attachment without text.') }}</div>
                            @if($message->attachment_url)
                                <a class="chat-attachment {{ $message->sender_type === 'customer' ? '' : 'light' }}" href="{{ $message->attachment_url }}" target="_blank">
                                    {{ $message->attachment_name ?: ($isAr ? 'فتح المرفق' : 'Open attachment') }}
                                </a>
                            @endif
                            <small>{{ $message->created_at?->format('Y-m-d H:i') }}</small>
                        </div>
                    @empty
                        <div class="chat-bubble chat-system" id="chat-empty-state">
                            <div>{{ $isAr ? 'ابدأ رسالتك الأولى وسيبدأ المساعد الذكي بالرد. يمكنك طلب التحويل لخدمة العملاء في أي وقت.' : 'Send your first message and the AI assistant will start responding. You can ask to transfer to customer service at any time.' }}</div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    <div id="chat-feedback" class="alert alert-danger d-none mb-3"></div>

                    <div class="chat-form-grid">
                        @if(! $conversation)
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label>
                                    <input class="form-control" id="chat-name" placeholder="{{ $isAr ? 'اكتب اسمك' : 'Enter your name' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label>
                                    <input class="form-control" id="chat-phone" placeholder="{{ $isAr ? 'رقم الهاتف' : 'Phone number' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                                    <input class="form-control" id="chat-email" type="email" placeholder="{{ $isAr ? 'اختياري' : 'Optional' }}">
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label">{{ $isAr ? 'رسالتك' : 'Your Message' }}</label>
                            <textarea class="form-control" rows="4" id="chat-message" placeholder="{{ $isAr ? 'اكتب سؤالك أو اطلب التحويل لخدمة العملاء' : 'Write your question or ask to transfer to customer service' }}"></textarea>
                        </div>
                        <div>
                            <label class="form-label">{{ $isAr ? 'مرفق' : 'Attachment' }}</label>
                            <input class="form-control" type="file" id="chat-attachment" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                            <small class="text-secondary">{{ $isAr ? 'عند كتابة مثل: أريد خدمة العملاء، سيتم إيقاف البوت وتحويل المحادثة لفريق الدعم.' : 'If you write something like: I want customer service, the bot will stop and the chat will be handed to support.' }}</small>
                            <button class="btn btn-primary px-4" type="button" id="send-chat-message">{{ $isAr ? 'إرسال' : 'Send' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-card">
            <div class="chat-card-head">
                <strong>{{ $isAr ? 'كيف يعمل الشات؟' : 'How it Works' }}</strong>
            </div>
            <div class="chat-card-body">
                <div class="chat-side-card mb-3">
                    <strong>{{ $isAr ? '1. المساعد الذكي' : '1. AI Assistant' }}</strong>
                    <div class="text-secondary small">{{ $isAr ? 'يرد على الأسئلة الأولية عن الحجز والخدمات والتوجيه.' : 'Handles first-line questions about booking, services, and guidance.' }}</div>
                </div>
                <div class="chat-side-card mb-3">
                    <strong>{{ $isAr ? '2. التحويل للبشر' : '2. Human Handoff' }}</strong>
                    <div class="text-secondary small">{{ $isAr ? 'بمجرد طلبك التحويل، تنتقل المحادثة إلى خدمة العملاء داخل الداشبورد.' : 'As soon as you request handoff, the conversation moves to customer service in the dashboard.' }}</div>
                </div>
                <div class="chat-side-card">
                    <strong>{{ $isAr ? '3. الاستمرار في نفس المحادثة' : '3. Continue Same Thread' }}</strong>
                    <div class="text-secondary small">{{ $isAr ? 'نفس الصفحة تحتفظ بالمحادثة الحالية على هذا المتصفح.' : 'This page keeps the same conversation thread in this browser.' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isAr = {{ $isAr ? 'true' : 'false' }};
    const sendUrl = @json(route('front.chat.store', app()->getLocale()));
    const feedUrl = @json(route('front.chat.feed', app()->getLocale()));
    const thread = document.getElementById('chat-thread');
    const feedback = document.getElementById('chat-feedback');
    const statusPill = document.getElementById('chat-status-pill');
    const sendButton = document.getElementById('send-chat-message');
    const messageInput = document.getElementById('chat-message');
    const attachmentInput = document.getElementById('chat-attachment');
    const nameInput = document.getElementById('chat-name');
    const phoneInput = document.getElementById('chat-phone');
    const emailInput = document.getElementById('chat-email');
    let lastMessageId = {{ (int) ($messages->max('id') ?? 0) }};
    let stream = null;

    const labels = {
        bot: isAr ? 'AI مساعد' : 'AI Assistant',
        human: isAr ? 'خدمة العملاء' : 'Human Support',
        closed: isAr ? 'مغلقة' : 'Closed',
    };

    const bubbleClass = (senderType) => `chat-bubble chat-${senderType}`;

    const appendMessage = (message) => {
        const empty = document.getElementById('chat-empty-state');
        if (empty) empty.remove();

        const wrapper = document.createElement('div');
        wrapper.className = bubbleClass(message.sender_type);
        const body = document.createElement('div');
        body.textContent = message.message || (isAr ? 'مرفق بدون نص.' : 'Attachment without text.');
        body.style.whiteSpace = 'pre-wrap';
        const meta = document.createElement('small');
        meta.textContent = message.created_at || '';
        wrapper.appendChild(body);
        if (message.attachment_url) {
            const attachment = document.createElement('a');
            attachment.className = `chat-attachment ${message.sender_type === 'customer' ? '' : 'light'}`;
            attachment.href = message.attachment_url;
            attachment.target = '_blank';
            attachment.textContent = message.attachment_name || (isAr ? 'فتح المرفق' : 'Open attachment');
            wrapper.appendChild(attachment);
        }
        wrapper.appendChild(meta);
        thread.appendChild(wrapper);
        thread.scrollTop = thread.scrollHeight;
    };

    const updateStatus = (conversation) => {
        if (!conversation || !statusPill) return;
        statusPill.className = `chat-status-pill ${conversation.status}`;
        statusPill.textContent = labels[conversation.status] || conversation.status;
    };

    const setError = (text) => {
        if (!text) {
            feedback.classList.add('d-none');
            feedback.textContent = '';
            return;
        }
        feedback.classList.remove('d-none');
        feedback.textContent = text;
    };

    const startStream = () => {
        if (stream) {
            stream.close();
        }
        stream = new EventSource(`${@json(route('front.chat.stream', app()->getLocale()))}?last_id=${lastMessageId}`);
        stream.addEventListener('chat-message', (event) => {
            const message = JSON.parse(event.data);
            appendMessage(message);
            lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
        });
        stream.addEventListener('conversation-state', (event) => {
            const conversation = JSON.parse(event.data);
            updateStatus(conversation);
        });
        stream.onerror = async () => {
            stream.close();
            try {
                const response = await fetch(`${feedUrl}?after_id=${lastMessageId}`, { headers: { 'Accept': 'application/json' } });
                if (response.ok) {
                    const payload = await response.json();
                    updateStatus(payload.conversation);
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

    sendButton?.addEventListener('click', async function () {
        setError('');
        sendButton.disabled = true;

        const formData = new FormData();
        formData.append('_token', @json(csrf_token()));
        formData.append('message', messageInput.value.trim());
        if (attachmentInput?.files?.[0]) {
            formData.append('attachment', attachmentInput.files[0]);
        }

        if (nameInput) formData.append('name', nameInput.value.trim());
        if (phoneInput) formData.append('phone', phoneInput.value.trim());
        if (emailInput) formData.append('email', emailInput.value.trim());

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json();
            if (!response.ok) {
                const errorText = payload?.message || Object.values(payload?.errors || {}).flat().join(' | ') || (isAr ? 'تعذر إرسال الرسالة.' : 'Unable to send message.');
                setError(errorText);
                return;
            }

            thread.innerHTML = '';
            (payload.messages || []).forEach((message) => {
                appendMessage(message);
                lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
            });
            updateStatus(payload.conversation);
            messageInput.value = '';
            if (attachmentInput) attachmentInput.value = '';
            startStream();
        } catch (error) {
            setError(isAr ? 'حدث خطأ أثناء الإرسال.' : 'A sending error occurred.');
        } finally {
            sendButton.disabled = false;
        }
    });

    startStream();
});
</script>
@endsection
