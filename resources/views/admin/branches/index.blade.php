@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الفروع' : 'Branches' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'مواقع العيادة وحالة التفعيل' : 'Clinic locations and active status' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.branches.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة فرع' : 'Add Branch' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'الاسم عربي' : 'Arabic Name' }}</th>
                    <th>{{ $isAr ? 'الاسم إنجليزي' : 'English Name' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($branches as $branch)
                    <tr>
                        <td>{{ $branch->id }}</td>
                        <td class="fw-semibold">{{ $branch->name_ar }}</td>
                        <td>{{ $branch->name_en }}</td>
                        <td>
                            <span class="admin-status-pill {{ $branch->is_active ? '' : 'is-muted' }}">
                                {{ $branch->is_active ? ($isAr ? 'مفعل' : 'Active') : ($isAr ? 'غير مفعل' : 'Inactive') }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.branches.edit', [app()->getLocale(), $branch]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.branches.destroy', [app()->getLocale(), $branch]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الفرع؟' : 'Delete branch?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty">{{ $isAr ? 'لا توجد فروع' : 'No branches found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $branches->links() }}</div>
</div>
@endsection
