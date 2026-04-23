@php
    $isAr = app()->getLocale() === 'ar';
    $senderLabels = [
        'customer' => $isAr ? 'أنت' : 'You',
        'admin' => $isAr ? 'خدمة العملاء' : 'Support',
        'ai' => $isAr ? 'المساعد الذكي' : 'AI Assistant',
        'system' => $isAr ? 'النظام' : 'System',
    ];
    $senderIcons = [
        'customer' => 'bi-person',
        'admin' => 'bi-headset',
        'ai' => 'bi-stars',
        'system' => 'bi-shield-check',
    ];
@endphp

<div class="chat-widget" id="front-chat-widget" aria-hidden="true">
    <div class="chat-widget-head">
        <div class="chat-widget-head-main">
            <div class="chat-widget-avatar">
                <i class="bi bi-headset"></i>
            </div>
            <div class="chat-widget-head-copy">
                <h3 class="chat-widget-title">{{ $isAr ? 'الدعم المباشر' : 'Live Support' }}</h3>
                <p class="chat-widget-subtitle">{{ $isAr ? 'شات جانبي مباشر بدون مغادرة الصفحة.' : 'Side chat without leaving the page.' }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="chat-widget-status {{ $conversation?->status ?? 'bot' }}" id="chat-widget-status">
                {{ $conversation?->status === 'human' ? ($isAr ? 'خدمة العملاء' : 'Human Support') : ($conversation?->status === 'closed' ? ($isAr ? 'مغلقة' : 'Closed') : ($isAr ? 'AI مساعد' : 'AI Assistant')) }}
            </span>
            <button type="button" class="chat-widget-close" id="chat-widget-close" aria-label="{{ $isAr ? 'إغلاق' : 'Close' }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="chat-widget-body">
        <div class="chat-widget-banner">
            <div class="chat-widget-banner-copy">
                <p class="chat-widget-banner-title">{{ $isAr ? 'رد ذكي وتحويل مباشر' : 'Smart reply and direct handoff' }}</p>
                <p class="chat-widget-banner-text">{{ $isAr ? 'ابدأ مع المساعد الذكي، وإذا احتجت موظفًا حقيقيًا يتم تحويلك لنفس المحادثة مباشرة.' : 'Start with AI, then get handed to a real agent in the same thread when needed.' }}</p>
            </div>
            <span class="chat-widget-badge">
                <i class="bi bi-stars"></i>
                {{ $isAr ? 'نشط الآن' : 'Online now' }}
            </span>
        </div>

        <div class="chat-widget-thread" id="chat-widget-thread">
            @forelse($messages as $message)
                <div class="chat-widget-message-row {{ $message->sender_type }}">
                    @if($message->sender_type !== 'customer')
                        <div class="chat-widget-message-avatar {{ $message->sender_type }}">
                            <i class="bi {{ $senderIcons[$message->sender_type] ?? 'bi-chat-dots' }}"></i>
                        </div>
                    @endif
                    <div class="chat-widget-message-stack">
                        <div class="chat-widget-message-meta">
                            <span>{{ $senderLabels[$message->sender_type] ?? $message->sender_type }}</span>
                            <span class="dot"></span>
                            <span>{{ $message->created_at?->format('H:i') }}</span>
                        </div>
                        <div class="chat-widget-bubble chat-widget-{{ $message->sender_type }}">
                            <div style="white-space: pre-wrap;">{{ $message->message ?: ($isAr ? 'مرفق بدون نص.' : 'Attachment without text.') }}</div>
                            @if($message->attachment_url)
                                <a class="chat-widget-attachment {{ $message->sender_type === 'customer' ? '' : 'light' }}" href="{{ $message->attachment_url }}" target="_blank">
                                    <i class="bi bi-paperclip"></i>
                                    {{ $message->attachment_name ?: ($isAr ? 'فتح المرفق' : 'Open attachment') }}
                                </a>
                            @endif
                            <small>{{ $message->created_at?->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                    @if($message->sender_type === 'customer')
                        <div class="chat-widget-message-avatar customer">
                            <i class="bi bi-person"></i>
                        </div>
                    @endif
                </div>
            @empty
                <div class="chat-widget-message-row system" id="chat-widget-empty-state">
                    <div class="chat-widget-message-avatar system">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div class="chat-widget-message-stack">
                        <div class="chat-widget-message-meta">
                            <span>{{ $isAr ? 'المساعد الذكي' : 'AI Assistant' }}</span>
                        </div>
                        <div class="chat-widget-bubble chat-widget-system">
                            <div>{{ $isAr ? 'ابدأ رسالتك الأولى وسيبدأ المساعد الذكي بالرد. ويمكنك طلب التحويل لخدمة العملاء في أي وقت.' : 'Send your first message and the AI assistant will reply. You can request human support at any time.' }}</div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="chat-widget-form">
            <div class="alert alert-danger chat-widget-feedback" id="chat-widget-feedback"></div>
            <div class="chat-widget-grid">
                @if(! $conversation)
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="chat-widget-input-group">
                                <label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label>
                                <input class="form-control" id="chat-widget-name" placeholder="{{ $isAr ? 'اكتب اسمك' : 'Enter your name' }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="chat-widget-input-group">
                                <label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label>
                                <input class="form-control" id="chat-widget-phone" placeholder="{{ $isAr ? 'رقم الهاتف' : 'Phone number' }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="chat-widget-input-group">
                                <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                                <input class="form-control" type="email" id="chat-widget-email" placeholder="{{ $isAr ? 'اختياري' : 'Optional' }}">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="chat-widget-input-group">
                    <label class="form-label">{{ $isAr ? 'رسالتك' : 'Your Message' }}</label>
                    <textarea class="form-control" id="chat-widget-message" placeholder="{{ $isAr ? 'اكتب سؤالك أو اطلب التحويل لخدمة العملاء' : 'Write your question or ask for human support' }}"></textarea>
                </div>

                <div class="chat-widget-input-group">
                    <label class="form-label">{{ $isAr ? 'مرفق' : 'Attachment' }}</label>
                    <div class="chat-widget-attachment-row">
                        <label class="chat-widget-upload-btn">
                            <i class="bi bi-paperclip"></i>
                            {{ $isAr ? 'إرفاق ملف' : 'Attach file' }}
                            <input type="file" id="chat-widget-attachment" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        </label>
                        <span class="chat-widget-file-name" id="chat-widget-file-name">{{ $isAr ? 'لم يتم اختيار ملف' : 'No file selected' }}</span>
                    </div>
                </div>
                <div class="chat-widget-actions">
                    <div class="chat-widget-note">{{ $isAr ? 'اكتب مثلًا: أريد التحدث مع خدمة العملاء للتحويل للبشر مباشرة.' : 'Write something like: I want customer service to hand off to a human.' }}</div>
                    <button class="btn btn-primary px-4 chat-widget-send" type="button" id="chat-widget-send">
                        <i class="bi bi-send-fill"></i>
                        {{ $isAr ? 'إرسال' : 'Send' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('front-chat-widget');
    const toggleButton = document.getElementById('chat-widget-toggle');
    const closeButton = document.getElementById('chat-widget-close');
    if (!widget || !toggleButton) return;

    const isAr = {{ $isAr ? 'true' : 'false' }};
    const sendUrl = {{ \Illuminate\Support\Js::from(route('front.chat.store', app()->getLocale())) }};
    const feedUrl = {{ \Illuminate\Support\Js::from(route('front.chat.feed', app()->getLocale())) }};
    const streamUrl = {{ \Illuminate\Support\Js::from(route('front.chat.stream', app()->getLocale())) }};
    const csrfToken = {{ \Illuminate\Support\Js::from(csrf_token()) }};
    const thread = document.getElementById('chat-widget-thread');
    const feedback = document.getElementById('chat-widget-feedback');
    const statusPill = document.getElementById('chat-widget-status');
    const sendButton = document.getElementById('chat-widget-send');
    const messageInput = document.getElementById('chat-widget-message');
    const attachmentInput = document.getElementById('chat-widget-attachment');
    const fileNameLabel = document.getElementById('chat-widget-file-name');
    const nameInput = document.getElementById('chat-widget-name');
    const phoneInput = document.getElementById('chat-widget-phone');
    const emailInput = document.getElementById('chat-widget-email');
    let lastMessageId = {{ (int) ($messages->max('id') ?? 0) }};
    let stream = null;
    let openedOnce = false;

    const labels = {
        bot: isAr ? 'AI مساعد' : 'AI Assistant',
        human: isAr ? 'خدمة العملاء' : 'Human Support',
        closed: isAr ? 'مغلقة' : 'Closed',
    };

    const setWidgetState = (isOpen) => {
        widget.classList.toggle('is-open', isOpen);
        widget.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (!isOpen) return;
        if (!openedOnce) {
            openedOnce = true;
            startStream();
        }
        setTimeout(() => {
            messageInput?.focus();
            thread.scrollTop = thread.scrollHeight;
        }, 90);
    };

    const setError = (text) => {
        if (!text) {
            feedback.classList.remove('is-visible');
            feedback.textContent = '';
            return;
        }
        feedback.classList.add('is-visible');
        feedback.textContent = text;
    };

    const appendMessage = (message) => {
        document.getElementById('chat-widget-empty-state')?.remove();
        const senderLabelMap = {
            customer: isAr ? 'أنت' : 'You',
            admin: isAr ? 'خدمة العملاء' : 'Support',
            ai: isAr ? 'المساعد الذكي' : 'AI Assistant',
            system: isAr ? 'النظام' : 'System',
        };
        const senderIconMap = {
            customer: 'bi-person',
            admin: 'bi-headset',
            ai: 'bi-stars',
            system: 'bi-shield-check',
        };
        const row = document.createElement('div');
        row.className = `chat-widget-message-row ${message.sender_type}`;

        const avatar = document.createElement('div');
        avatar.className = `chat-widget-message-avatar ${message.sender_type}`;
        avatar.innerHTML = `<i class="bi ${senderIconMap[message.sender_type] || 'bi-chat-dots'}"></i>`;

        const stack = document.createElement('div');
        stack.className = 'chat-widget-message-stack';

        const meta = document.createElement('div');
        meta.className = 'chat-widget-message-meta';
        meta.innerHTML = `<span>${senderLabelMap[message.sender_type] || message.sender_type}</span><span class="dot"></span><span>${(message.created_at || '').slice(11, 16)}</span>`;

        const wrapper = document.createElement('div');
        wrapper.className = `chat-widget-bubble chat-widget-${message.sender_type}`;

        const body = document.createElement('div');
        body.style.whiteSpace = 'pre-wrap';
        body.textContent = message.message || (isAr ? 'مرفق بدون نص.' : 'Attachment without text.');
        wrapper.appendChild(body);

        if (message.attachment_url) {
            const attachment = document.createElement('a');
            attachment.className = `chat-widget-attachment ${message.sender_type === 'customer' ? '' : 'light'}`;
            attachment.href = message.attachment_url;
            attachment.target = '_blank';
            attachment.innerHTML = `<i class="bi bi-paperclip"></i> ${message.attachment_name || (isAr ? 'فتح المرفق' : 'Open attachment')}`;
            wrapper.appendChild(attachment);
        }

        const small = document.createElement('small');
        small.textContent = message.created_at || '';
        wrapper.appendChild(small);
        stack.appendChild(meta);
        stack.appendChild(wrapper);

        if (message.sender_type === 'customer') {
            row.appendChild(stack);
            row.appendChild(avatar);
        } else {
            row.appendChild(avatar);
            row.appendChild(stack);
        }

        thread.appendChild(row);
        thread.scrollTop = thread.scrollHeight;
    };

    const updateStatus = (conversation) => {
        if (!conversation) return;
        statusPill.className = `chat-widget-status ${conversation.status}`;
        statusPill.textContent = labels[conversation.status] || conversation.status;
    };

    const startStream = () => {
        stream?.close();
        stream = new EventSource(`${streamUrl}?last_id=${lastMessageId}`);
        stream.addEventListener('chat-message', (event) => {
            const message = JSON.parse(event.data);
            appendMessage(message);
            lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
        });
        stream.addEventListener('conversation-state', (event) => updateStatus(JSON.parse(event.data)));
        stream.onerror = async () => {
            stream?.close();
            stream = null;
            try {
                const response = await fetch(`${feedUrl}?after_id=${lastMessageId}`, { headers: { Accept: 'application/json' } });
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
            setTimeout(() => widget.classList.contains('is-open') && startStream(), 3000);
        };
    };

    toggleButton.addEventListener('click', () => setWidgetState(!widget.classList.contains('is-open')));
    closeButton?.addEventListener('click', () => setWidgetState(false));
    document.addEventListener('keydown', (event) => event.key === 'Escape' && widget.classList.contains('is-open') && setWidgetState(false));
    attachmentInput?.addEventListener('change', () => {
        if (!fileNameLabel) return;
        fileNameLabel.textContent = attachmentInput.files?.[0]?.name || (isAr ? 'لم يتم اختيار ملف' : 'No file selected');
    });

    sendButton?.addEventListener('click', async () => {
        setError('');
        sendButton.disabled = true;
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('message', messageInput?.value?.trim() || '');
        if (attachmentInput?.files?.[0]) formData.append('attachment', attachmentInput.files[0]);
        if (nameInput) formData.append('name', nameInput.value.trim());
        if (phoneInput) formData.append('phone', phoneInput.value.trim());
        if (emailInput) formData.append('email', emailInput.value.trim());

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });
            const payload = await response.json();
            if (!response.ok) {
                setError(payload?.message || Object.values(payload?.errors || {}).flat().join(' | ') || (isAr ? 'تعذر إرسال الرسالة.' : 'Unable to send message.'));
                return;
            }
            thread.innerHTML = '';
            (payload.messages || []).forEach((message) => {
                appendMessage(message);
                lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
            });
            updateStatus(payload.conversation);
            if (messageInput) messageInput.value = '';
            if (attachmentInput) attachmentInput.value = '';
            if (fileNameLabel) fileNameLabel.textContent = isAr ? 'لم يتم اختيار ملف' : 'No file selected';
            nameInput?.closest('.row')?.remove();
            if (!stream && widget.classList.contains('is-open')) startStream();
        } catch (error) {
            setError(isAr ? 'حدث خطأ أثناء الإرسال.' : 'A sending error occurred.');
        } finally {
            sendButton.disabled = false;
        }
    });
});
</script>
