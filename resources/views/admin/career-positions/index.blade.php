@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الوظائف' : 'Career Positions' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'الوظائف المفتوحة والأقسام وأنواع العقود' : 'Open positions, departments, and job types' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.career-positions.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة وظيفة' : 'Add Position' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'المسمى' : 'Title' }}</th>
                    <th>{{ $isAr ? 'القسم' : 'Department' }}</th>
                    <th>{{ $isAr ? 'النوع' : 'Type' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($positions as $position)
                    <tr>
                        <td>{{ $position->id }}</td>
                        <td class="fw-semibold">{{ $isAr ? ($position->title_ar ?: $position->title_en) : $position->title_en }}</td>
                        <td>{{ $isAr ? ($position->department_ar ?: $position->department_en) : $position->department_en }}</td>
                        <td><span class="admin-status-pill is-muted">{{ $position->job_type }}</span></td>
                        <td>
                            <span class="admin-status-pill {{ $position->is_active ? '' : 'is-muted' }}">
                                {{ $position->is_active ? ($isAr ? 'مفعل' : 'Active') : ($isAr ? 'غير مفعل' : 'Inactive') }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.career-positions.show', [app()->getLocale(), $position]) }}">{{ $isAr ? 'عرض' : 'Show' }}</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.career-positions.edit', [app()->getLocale(), $position]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.career-positions.destroy', [app()->getLocale(), $position]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الوظيفة؟' : 'Delete position?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty">{{ $isAr ? 'لا توجد وظائف' : 'No positions found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $positions->links() }}</div>
</div>
@endsection
