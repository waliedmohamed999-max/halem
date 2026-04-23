@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'آراء المرضى' : 'Testimonials' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'التقييمات المعروضة على الواجهة' : 'Testimonials displayed on the website' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.testimonials.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة رأي' : 'Add Testimonial' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                    <th>{{ $isAr ? 'التقييم' : 'Rating' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($testimonials as $testimonial)
                    <tr>
                        <td class="fw-semibold">{{ $testimonial->name }}</td>
                        <td><span class="admin-status-pill">{{ $testimonial->rating }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.testimonials.edit', [app()->getLocale(), $testimonial]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="admin-empty">{{ $isAr ? 'لا توجد آراء' : 'No testimonials found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $testimonials->links() }}</div>
</div>
@endsection
