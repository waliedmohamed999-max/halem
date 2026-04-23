@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الصفحات' : 'Pages' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'إدارة الصفحات الثابتة ومحتواها' : 'Manage static pages and their content' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.pages.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة صفحة' : 'Add Page' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الرابط' : 'Slug' }}</th>
                    <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td><span class="admin-status-pill is-muted">{{ $page->slug }}</span></td>
                        <td class="fw-semibold">{{ $isAr ? ($page->title_ar ?: $page->title_en) : $page->title_en }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.pages.edit', [app()->getLocale(), $page]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="admin-empty">{{ $isAr ? 'لا توجد صفحات' : 'No pages found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $pages->links() }}</div>
</div>
@endsection
