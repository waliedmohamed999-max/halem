@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'ساعات العمل' : 'Working Hours' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'جداول الفروع والحالات الطارئة' : 'Branch schedules and emergency availability' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.working-hours.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة موعد' : 'Add Schedule' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الفرع' : 'Branch' }}</th>
                    <th>{{ $isAr ? 'اليوم' : 'Day' }}</th>
                    <th>{{ $isAr ? 'الدوام' : 'Hours' }}</th>
                    <th>{{ $isAr ? 'الطوارئ' : 'Emergency' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($hours as $hour)
                    <tr>
                        <td class="fw-semibold">{{ optional($hour->branch)->name_ar ?? optional($hour->branch)->name_en ?? ($isAr ? 'عام' : 'General') }}</td>
                        <td>{{ $isAr ? ($hour->day_label_ar ?? $hour->day_label_en) : $hour->day_label_en }}</td>
                        <td>{{ $hour->is_open ? ($hour->open_at.' - '.$hour->close_at) : ($isAr ? 'مغلق' : 'Closed') }}</td>
                        <td>
                            <span class="admin-status-pill {{ $hour->is_emergency ? '' : 'is-muted' }}">
                                {{ $hour->is_emergency ? ($isAr ? 'متاح' : 'Available') : ($isAr ? 'لا' : 'No') }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.working-hours.edit', [app()->getLocale(), $hour]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty">{{ $isAr ? 'لا توجد مواعيد عمل' : 'No working hours found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $hours->links() }}</div>
</div>
@endsection
