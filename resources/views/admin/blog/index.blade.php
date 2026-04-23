@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'المقالات' : 'Blog Posts' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'إدارة المقالات المنشورة والمسودات' : 'Manage published posts and drafts' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.blog-posts.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة مقال' : 'Add Post' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td class="fw-semibold">{{ $isAr ? ($post->title_ar ?: $post->title_en) : $post->title_en }}</td>
                        <td><span class="admin-status-pill">{{ $post->status }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.blog-posts.edit', [app()->getLocale(), $post]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="admin-empty">{{ $isAr ? 'لا توجد مقالات حتى الآن' : 'No posts found yet' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $posts->links() }}</div>
</div>
@endsection
