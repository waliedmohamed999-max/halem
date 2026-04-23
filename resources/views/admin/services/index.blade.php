@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الخدمات' : 'Services' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'الخدمات المعروضة وحالة التمييز' : 'Published services and featured status' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.services.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة خدمة' : 'Add Service' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'الخدمة' : 'Service' }}</th>
                    <th>{{ $isAr ? 'مميز' : 'Featured' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td class="fw-semibold">{{ $isAr ? ($service->title_ar ?: $service->title_en) : $service->title_en }}</td>
                        <td>
                            <span class="admin-status-pill {{ $service->is_featured ? '' : 'is-muted' }}">
                                {{ $service->is_featured ? ($isAr ? 'مميز' : 'Featured') : ($isAr ? 'عادي' : 'Standard') }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.services.edit', [app()->getLocale(), $service]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.services.destroy', [app()->getLocale(), $service]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الخدمة؟' : 'Delete service?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="admin-empty">{{ $isAr ? 'لا توجد خدمات' : 'No services found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $services->links() }}</div>
</div>
@endsection
