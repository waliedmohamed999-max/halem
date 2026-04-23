@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $roleCount = $roles->count();
    $filteredUsers = $users->count();
    $allowedUsers = $users->filter(fn ($user) => $user->can('access-admin-dashboard'))->count();
@endphp

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'إدارة المستخدمين والصلاحيات' : 'Users & Permissions' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'الوصول، الأدوار، وصلاحيات لوحة التحكم في شاشة موحدة' : 'Access, roles, and dashboard permissions in one unified workspace' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-primary" href="{{ route('admin.users.permissions.matrix', app()->getLocale()) }}">{{ $isAr ? 'مصفوفة الصلاحيات' : 'Permissions Matrix' }}</a>
            <a class="btn btn-primary" href="{{ route('admin.users.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة مستخدم' : 'Add User' }}</a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-panel"><small>{{ $isAr ? 'إجمالي المستخدمين' : 'Total Users' }}</small><strong>{{ $users->total() }}</strong><span>{{ $isAr ? 'كل الحسابات المسجلة' : 'All registered accounts' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'الأدوار المتاحة' : 'Available Roles' }}</small><strong>{{ $roleCount }}</strong><span>{{ $isAr ? 'مجموع الأدوار المعرفة' : 'Total configured roles' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'وصول للداشبورد' : 'Dashboard Access' }}</small><strong>{{ $allowedUsers }}</strong><span>{{ $isAr ? 'ضمن النتائج الحالية' : 'Within current results' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'نتائج الفلتر' : 'Filtered Results' }}</small><strong>{{ $filteredUsers }}</strong><span>{{ $isAr ? 'المعروض في هذه الصفحة' : 'Shown on this page' }}</span></div>
    </div>

    <div class="admin-filter-card">
        <form class="row g-2" method="GET" action="{{ route('admin.users.index', app()->getLocale()) }}">
            <div class="col-md-6">
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="{{ $isAr ? 'بحث بالاسم أو الإيميل أو الهاتف' : 'Search by name, email, or phone' }}">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="role">
                    <option value="">{{ $isAr ? 'كل الأدوار' : 'All roles' }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('admin.users.index', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                    <th>{{ $isAr ? 'البريد' : 'Email' }}</th>
                    <th>{{ $isAr ? 'الهاتف' : 'Phone' }}</th>
                    <th>{{ $isAr ? 'الدور' : 'Role' }}</th>
                    <th>{{ $isAr ? 'وصول الداشبورد' : 'Dashboard Access' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    @php($userRoles = $user->getRoleNames())
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?: '-' }}</td>
                        <td>
                            @if($userRoles->isEmpty())
                                <span class="admin-status-pill is-muted">{{ $isAr ? 'بدون دور' : 'No Role' }}</span>
                            @else
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($userRoles as $role)
                                        <span class="admin-status-pill">{{ $role }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="admin-status-pill {{ $user->can('access-admin-dashboard') ? '' : 'is-danger' }}">
                                {{ $user->can('access-admin-dashboard') ? ($isAr ? 'مسموح' : 'Allowed') : ($isAr ? 'محجوب' : 'Blocked') }}
                            </span>
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', [app()->getLocale(), $user->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.permissions.edit', [app()->getLocale(), $user->id]) }}">{{ $isAr ? 'الصلاحيات' : 'Permissions' }}</a>
                                @if((int) auth()->id() !== (int) $user->id)
                                    <form method="POST" action="{{ route('admin.users.destroy', [app()->getLocale(), $user->id]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف المستخدم؟' : 'Delete this user?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="admin-empty">{{ $isAr ? 'لا يوجد مستخدمون' : 'No users found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection
