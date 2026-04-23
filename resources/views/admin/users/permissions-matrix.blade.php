@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $roles = $users
        ->flatMap(fn ($u) => $u->roles->pluck('name'))
        ->unique()
        ->sort()
        ->values();
    $permissionLabels = [
        'access-admin-dashboard' => ['ar' => 'دخول لوحة التحكم', 'en' => 'Dashboard Access'],
        'manage-content' => ['ar' => 'إدارة المحتوى العام', 'en' => 'Manage Content'],
        'manage-home-sections' => ['ar' => 'أقسام الرئيسية', 'en' => 'Home Sections'],
        'manage-marketing-sections' => ['ar' => 'البنرات والعروض', 'en' => 'Banners & Offers'],
        'manage-branches' => ['ar' => 'الفروع', 'en' => 'Branches'],
        'manage-working-hours' => ['ar' => 'ساعات العمل', 'en' => 'Working Hours'],
        'manage-services' => ['ar' => 'الخدمات', 'en' => 'Services'],
        'manage-doctors' => ['ar' => 'الأطباء', 'en' => 'Doctors'],
        'manage-blog' => ['ar' => 'المدونة', 'en' => 'Blog'],
        'manage-pages' => ['ar' => 'الصفحات', 'en' => 'Pages'],
        'manage-faqs' => ['ar' => 'الأسئلة الشائعة', 'en' => 'FAQs'],
        'manage-testimonials' => ['ar' => 'آراء المرضى', 'en' => 'Testimonials'],
        'manage-careers' => ['ar' => 'الوظائف', 'en' => 'Careers'],
        'manage-career-applications' => ['ar' => 'طلبات التوظيف', 'en' => 'Career Applications'],
        'manage-appointments' => ['ar' => 'الحجوزات', 'en' => 'Appointments'],
        'manage-finance' => ['ar' => 'المالية', 'en' => 'Finance'],
        'manage-messages' => ['ar' => 'الرسائل', 'en' => 'Messages'],
        'manage-subscribers' => ['ar' => 'المشتركين', 'en' => 'Subscribers'],
        'manage-patient-records' => ['ar' => 'ملفات المرضى', 'en' => 'Patient Records'],
        'manage-users' => ['ar' => 'المستخدمون', 'en' => 'Users'],
        'manage-settings' => ['ar' => 'الإعدادات', 'en' => 'Settings'],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">{{ $isAr ? 'مصفوفة الصلاحيات (المستخدمون × الصلاحيات)' : 'Permissions Matrix (Users x Permissions)' }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index', app()->getLocale()) }}">{{ $isAr ? 'العودة للمستخدمين' : 'Back to users' }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.users.permissions.matrix.update', app()->getLocale()) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label mb-1">{{ $isAr ? 'بحث باسم المستخدم' : 'Search user name' }}</label>
                    <input id="userSearch" type="text" class="form-control form-control-sm" placeholder="{{ $isAr ? 'اكتب الاسم...' : 'Type user name...' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label mb-1">{{ $isAr ? 'فلتر حسب الدور' : 'Filter by role' }}</label>
                    <select id="roleFilter" class="form-select form-select-sm">
                        <option value="all">{{ $isAr ? 'كل الأدوار' : 'All roles' }}</option>
                        @foreach($roles as $role)
                            <option value="{{ strtolower($role) }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="toggleVisible(true)">{{ $isAr ? 'تحديد الكل (الظاهر)' : 'Select all (visible)' }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="toggleVisible(false)">{{ $isAr ? 'إلغاء الكل (الظاهر)' : 'Clear all (visible)' }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th class="sticky-top" style="min-width: 240px;">{{ $isAr ? 'المستخدم' : 'User' }}</th>
                    @foreach($permissions as $permission)
                        <th class="text-center sticky-top" style="min-width: 150px;">
                            <div>{{ $permissionLabels[$permission->name][$isAr ? 'ar' : 'en'] ?? $permission->name }}</div>
                            <div class="small text-muted"><code>{{ $permission->name }}</code></div>
                            <div class="d-flex justify-content-center gap-1 mt-1">
                                <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" onclick="toggleColumn({{ $loop->index }}, true)">+</button>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" onclick="toggleColumn({{ $loop->index }}, false)">-</button>
                            </div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    @php
                        $direct = $user->permissions->pluck('name')->flip();
                        $fromRoles = $user->roles->flatMap(fn ($role) => $role->permissions->pluck('name'))->unique()->flip();
                        $roleNames = $user->roles->pluck('name')->implode(',');
                    @endphp
                    <tr
                        class="matrix-row"
                        data-user-name="{{ strtolower($user->name) }}"
                        data-user-roles="{{ strtolower($roleNames) }}"
                    >
                        <td>
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                    @if($user->roles->isNotEmpty())
                                        <div class="mt-1">
                                            @foreach($user->roles as $role)
                                                <span class="badge text-bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" onclick="toggleRow({{ $user->id }}, true)">+</button>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" onclick="toggleRow({{ $user->id }}, false)">-</button>
                                </div>
                            </div>
                        </td>
                        @foreach($permissions as $permission)
                            @php($name = $permission->name)
                            <td class="text-center">
                                <input
                                    class="form-check-input matrix-checkbox row-user-{{ $user->id }} col-index-{{ $loop->index }}"
                                    type="checkbox"
                                    name="matrix[{{ $user->id }}][{{ $name }}]"
                                    value="1"
                                    @checked(isset($direct[$name]))
                                >
                                @if(isset($fromRoles[$name]))
                                    <div class="small text-success mt-1">{{ $isAr ? 'من الدور' : 'From role' }}</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-success">{{ $isAr ? 'حفظ كل التعديلات' : 'Save all changes' }}</button>
    </div>
</form>

<script>
    function isVisibleRow(row) {
        return !row.classList.contains('d-none');
    }

    function toggleVisible(state) {
        document.querySelectorAll('.matrix-row').forEach(function (row) {
            if (!isVisibleRow(row)) return;
            row.querySelectorAll('.matrix-checkbox').forEach(function (checkbox) {
                checkbox.checked = state;
            });
        });
    }

    function toggleRow(userId, state) {
        document.querySelectorAll('.row-user-' + userId).forEach(function (checkbox) {
            checkbox.checked = state;
        });
    }

    function toggleColumn(colIndex, state) {
        document.querySelectorAll('.matrix-row').forEach(function (row) {
            if (!isVisibleRow(row)) return;
            row.querySelectorAll('.col-index-' + colIndex).forEach(function (checkbox) {
                checkbox.checked = state;
            });
        });
    }

    function applyFilters() {
        var searchInput = document.getElementById('userSearch');
        var roleFilter = document.getElementById('roleFilter');
        var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var role = roleFilter ? roleFilter.value : 'all';

        document.querySelectorAll('.matrix-row').forEach(function (row) {
            var name = row.getAttribute('data-user-name') || '';
            var roles = row.getAttribute('data-user-roles') || '';
            var matchesName = query === '' || name.indexOf(query) !== -1;
            var matchesRole = role === 'all' || roles.split(',').includes(role);
            row.classList.toggle('d-none', !(matchesName && matchesRole));
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('userSearch');
        var roleFilter = document.getElementById('roleFilter');
        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (roleFilter) roleFilter.addEventListener('change', applyFilters);
    });
</script>
@endsection
