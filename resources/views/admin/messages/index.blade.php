@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'المحادثات المباشرة' : 'Live Conversations' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'محادثات العملاء مع البوت وخدمة العملاء في مكان واحد.' : 'Customer conversations with bot and human support in one place.' }}</p>
        </div>
    </div>

    <div class="admin-list-card mb-3">
        <form class="row g-2" method="GET">
            <div class="col-md-5">
                <input class="form-control" name="search" value="{{ request('search') }}" placeholder="{{ $isAr ? 'بحث بالاسم أو الهاتف أو البريد' : 'Search by name, phone, or email' }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">{{ $isAr ? 'كل الحالات' : 'All statuses' }}</option>
                    <option value="bot" @selected(request('status') === 'bot')>{{ $isAr ? 'AI' : 'AI' }}</option>
                    <option value="human" @selected(request('status') === 'human')>{{ $isAr ? 'بشري' : 'Human' }}</option>
                    <option value="closed" @selected(request('status') === 'closed')>{{ $isAr ? 'مغلقة' : 'Closed' }}</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
            </div>
            <div class="col-md-2 d-grid">
                <a class="btn btn-outline-secondary" href="{{ route('admin.messages.index', app()->getLocale()) }}">{{ $isAr ? 'إعادة ضبط' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'العميل' : 'Customer' }}</th>
                    <th>{{ $isAr ? 'آخر رسالة' : 'Last Message' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'غير مقروء للأدمن' : 'Unread for Admin' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($conversations as $conversation)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $conversation->visitor_name }}</div>
                            <div class="small text-secondary">{{ $conversation->visitor_phone }}</div>
                            @if($conversation->visitor_email)
                                <div class="small text-secondary">{{ $conversation->visitor_email }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $conversation->last_message_preview ?: '-' }}</div>
                            <div class="small text-secondary">{{ $conversation->last_message_at?->format('Y-m-d H:i') ?: '-' }}</div>
                        </td>
                        <td><span class="admin-status-pill">{{ $conversation->status }}</span></td>
                        <td>
                            @if($conversation->admin_unread_count > 0)
                                <span class="badge text-bg-danger">{{ $conversation->admin_unread_count }}</span>
                            @else
                                <span class="text-secondary">0</span>
                            @endif
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.messages.show', [app()->getLocale(), $conversation]) }}">{{ $isAr ? 'فتح المحادثة' : 'Open Chat' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty">{{ $isAr ? 'لا توجد محادثات حتى الآن' : 'No conversations yet' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $conversations->links() }}</div>
</div>
@endsection
