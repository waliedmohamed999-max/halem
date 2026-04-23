@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $isEdit = isset($user);
    $selectedRole = old('role', $isEdit ? $user->getRoleNames()->first() : '');
    $selectedPermissions = old('permissions', $userDirectPermissions ?? []);
    $permissionLabels = [
        'access-admin-dashboard' => ['ar' => 'دخول لوحة التحكم', 'en' => 'Dashboard Access'],
        'manage-content' => ['ar' => 'إدارة المحتوى العام', 'en' => 'Manage Content'],
        'manage-home-sections' => ['ar' => 'إدارة أقسام الرئيسية', 'en' => 'Manage Home Sections'],
        'manage-marketing-sections' => ['ar' => 'إدارة البنرات والعروض', 'en' => 'Manage Marketing Sections'],
        'manage-branches' => ['ar' => 'إدارة الفروع', 'en' => 'Manage Branches'],
        'manage-working-hours' => ['ar' => 'إدارة ساعات العمل', 'en' => 'Manage Working Hours'],
        'manage-services' => ['ar' => 'إدارة الخدمات', 'en' => 'Manage Services'],
        'manage-doctors' => ['ar' => 'إدارة الأطباء', 'en' => 'Manage Doctors'],
        'manage-blog' => ['ar' => 'إدارة المدونة', 'en' => 'Manage Blog'],
        'manage-pages' => ['ar' => 'إدارة الصفحات', 'en' => 'Manage Pages'],
        'manage-faqs' => ['ar' => 'إدارة الأسئلة الشائعة', 'en' => 'Manage FAQs'],
        'manage-testimonials' => ['ar' => 'إدارة آراء المرضى', 'en' => 'Manage Testimonials'],
        'manage-careers' => ['ar' => 'إدارة الوظائف', 'en' => 'Manage Careers'],
        'manage-career-applications' => ['ar' => 'إدارة طلبات التوظيف', 'en' => 'Manage Career Applications'],
        'manage-appointments' => ['ar' => 'إدارة الحجوزات', 'en' => 'Manage Appointments'],
        'manage-finance' => ['ar' => 'إدارة المالية', 'en' => 'Manage Finance'],
        'manage-messages' => ['ar' => 'إدارة الرسائل', 'en' => 'Manage Messages'],
        'manage-subscribers' => ['ar' => 'إدارة المشتركين', 'en' => 'Manage Subscribers'],
        'manage-patient-records' => ['ar' => 'إدارة ملفات المرضى', 'en' => 'Manage Patient Records'],
        'manage-users' => ['ar' => 'إدارة المستخدمين', 'en' => 'Manage Users'],
        'manage-settings' => ['ar' => 'إدارة الإعدادات', 'en' => 'Manage Settings'],
    ];
    $groupTitles = [
        'dashboard' => $isAr ? 'دخول الداشبورد' : 'Dashboard Access',
        'content' => $isAr ? 'أقسام المحتوى' : 'Content Sections',
        'operations' => $isAr ? 'التشغيل والمتابعة' : 'Operations',
        'administration' => $isAr ? 'الإدارة العليا' : 'Administration',
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">{{ $isEdit ? ($isAr ? 'تعديل المستخدم' : 'Edit User') : ($isAr ? 'إنشاء مستخدم جديد' : 'Create User') }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index', app()->getLocale()) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form id="user-form" method="POST" action="{{ $isEdit ? route('admin.users.update', [app()->getLocale(), $user->id]) : route('admin.users.store', app()->getLocale()) }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label>
                    <input class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <input class="form-control" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label>
                    <input class="form-control" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'كلمة المرور' : 'Password' }}</label>
                    <input type="password" class="form-control" name="password" {{ $isEdit ? '' : 'required' }}>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'تأكيد كلمة المرور' : 'Confirm password' }}</label>
                    <input type="password" class="form-control" name="password_confirmation" {{ $isEdit ? '' : 'required' }}>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الدور (Role)' : 'Role' }}</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">{{ $isAr ? 'اختر الدور' : 'Select role' }}</option>
                        @foreach($roles as $role)
                            <option
                                value="{{ $role->name }}"
                                data-permissions-count="{{ (int) $role->permissions_count }}"
                                @selected($selectedRole === $role->name)
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="allow_dashboard_access"
                            name="allow_dashboard_access"
                            value="1"
                            @checked(old('allow_dashboard_access', $isEdit ? $user->can('access-admin-dashboard') : false))
                        >
                        <label class="form-check-label" for="allow_dashboard_access">
                            {{ $isAr ? 'السماح بدخول لوحة التحكم لهذا المستخدم' : 'Allow dashboard access for this user' }}
                        </label>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ $isAr ? 'الصلاحيات المباشرة (Direct Permissions)' : 'Direct Permissions' }}</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllDirect(true)">{{ $isAr ? 'تحديد الكل' : 'Select all' }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllDirect(false)">{{ $isAr ? 'إلغاء الكل' : 'Clear all' }}</button>
                </div>
            </div>
            <div class="row g-3">
                @foreach($groupedPermissions as $group => $permissions)
                    <div class="col-lg-6">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $groupTitles[$group] ?? $group }}</strong>
                                <button type="button" class="btn btn-sm btn-outline-dark" onclick="toggleGroup('{{ $group }}')">
                                    {{ $isAr ? 'تحديد القسم' : 'Toggle group' }}
                                </button>
                            </div>
                            @foreach($permissions as $permission)
                                @if($allPermissions->contains($permission))
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input direct-permission perm-group-{{ $group }}"
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission }}"
                                            id="perm_{{ $permission }}"
                                            @checked(in_array($permission, $selectedPermissions, true))
                                        >
                                        <label class="form-check-label" for="perm_{{ $permission }}">
                                            <span>{{ $permissionLabels[$permission][$isAr ? 'ar' : 'en'] ?? $permission }}</span>
                                            <div class="small text-muted"><code>{{ $permission }}</code></div>
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="permissions-warning" class="alert alert-warning mt-3 mb-0 d-none">
                {{ $isAr ? 'لا يمكن حفظ المستخدم بدون أي صلاحية. اختر دورًا يملك صلاحيات أو حدّد صلاحيات مباشرة أو فعّل دخول لوحة التحكم.' : 'Cannot save user without permissions. Choose a role with permissions, select direct permissions, or enable dashboard access.' }}
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <strong>{{ $isAr ? 'مهم:' : 'Important:' }}</strong>
                {{ $isAr ? 'التحقق يعتمد على: صلاحيات الدور + الصلاحيات المباشرة + صلاحية دخول لوحة التحكم.' : 'Validation is based on: role permissions + direct permissions + dashboard access permission.' }}
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index', app()->getLocale()) }}">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
                <button class="btn btn-success">{{ $isEdit ? ($isAr ? 'تحديث' : 'Update') : ($isAr ? 'حفظ' : 'Save') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var form = document.getElementById('user-form');
        if (!form) return;

        var roleSelect = document.getElementById('role');
        var dashboardSwitch = document.getElementById('allow_dashboard_access');
        var warning = document.getElementById('permissions-warning');

        function selectedRoleHasPermissions() {
            if (!roleSelect || roleSelect.selectedIndex < 0) return false;
            var selectedOption = roleSelect.options[roleSelect.selectedIndex];
            var count = parseInt(selectedOption.getAttribute('data-permissions-count') || '0', 10);
            return count > 0;
        }

        function selectedDirectPermissionsCount() {
            return document.querySelectorAll('.direct-permission:checked').length;
        }

        function validatePermissionAvailability() {
            var isValid = selectedRoleHasPermissions()
                || selectedDirectPermissionsCount() > 0
                || (dashboardSwitch && dashboardSwitch.checked);

            if (warning) warning.classList.toggle('d-none', isValid);
            return isValid;
        }

        window.toggleAllDirect = function (state) {
            document.querySelectorAll('.direct-permission').forEach(function (el) { el.checked = state; });
            validatePermissionAvailability();
        };

        window.toggleGroup = function (group) {
            var items = document.querySelectorAll('.perm-group-' + group);
            if (!items.length) return;
            var allChecked = Array.from(items).every(function (el) { return el.checked; });
            items.forEach(function (el) { el.checked = !allChecked; });
            validatePermissionAvailability();
        };

        if (roleSelect) roleSelect.addEventListener('change', validatePermissionAvailability);
        if (dashboardSwitch) dashboardSwitch.addEventListener('change', validatePermissionAvailability);
        document.querySelectorAll('.direct-permission').forEach(function (el) {
            el.addEventListener('change', validatePermissionAvailability);
        });

        form.addEventListener('submit', function (event) {
            if (!validatePermissionAvailability()) {
                event.preventDefault();
            }
        });

        validatePermissionAvailability();
    })();
</script>
@endsection
