@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'المشتركون' : 'Subscribers' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'قائمة البريد والتصدير والحذف' : 'Mailing list, export, and cleanup' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-primary" href="{{ route('admin.subscribers.export', app()->getLocale()) }}">{{ $isAr ? 'تصدير CSV' : 'CSV Export' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</th>
                    <th>{{ $isAr ? 'تاريخ الإضافة' : 'Created At' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subscribers as $subscriber)
                    <tr>
                        <td class="fw-semibold">{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->created_at }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <form method="POST" action="{{ route('admin.subscribers.destroy', [app()->getLocale(), $subscriber]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف المشترك؟' : 'Delete subscriber?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="admin-empty">{{ $isAr ? 'لا يوجد مشتركون' : 'No subscribers found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $subscribers->links() }}</div>
</div>
@endsection
