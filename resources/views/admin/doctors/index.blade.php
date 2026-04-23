@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'الأطباء' : 'Doctors' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'الفريق الطبي والتخصصات' : 'Medical team and specialties' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.doctors.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة طبيب' : 'Add Doctor' }}</a>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                    <th>{{ $isAr ? 'التخصص' : 'Specialty' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td>{{ $doctor->id }}</td>
                        <td class="fw-semibold">{{ $isAr ? ($doctor->name_ar ?: $doctor->name_en) : $doctor->name_en }}</td>
                        <td>{{ $isAr ? ($doctor->specialty_ar ?: $doctor->specialty_en) : $doctor->specialty_en }}</td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.doctors.edit', [app()->getLocale(), $doctor]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.doctors.destroy', [app()->getLocale(), $doctor]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الطبيب؟' : 'Delete doctor?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="admin-empty">{{ $isAr ? 'لا يوجد أطباء' : 'No doctors found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $doctors->links() }}</div>
</div>
@endsection
