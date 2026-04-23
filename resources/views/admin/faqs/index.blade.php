@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الأسئلة الشائعة' : 'FAQs' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'الأسئلة المنشورة وإجاباتها' : 'Published questions and answers' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.faqs.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة سؤال' : 'Add FAQ' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'السؤال' : 'Question' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($faqs as $faq)
                    <tr>
                        <td class="fw-semibold">{{ $isAr ? ($faq->question_ar ?: $faq->question_en) : $faq->question_en }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.faqs.edit', [app()->getLocale(), $faq]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="admin-empty">{{ $isAr ? 'لا توجد أسئلة' : 'No FAQs found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $faqs->links() }}</div>
</div>
@endsection
