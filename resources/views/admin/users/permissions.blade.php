@extends('layouts.admin')

@section('content')
@php
    $isAr = isset($isAr) ? (bool) $isAr : app()->getLocale() === 'ar';
    $currentPermissions = $user->getDirectPermissions()->pluck('name')->all();
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
    <h4 class="mb-0">{{ $isAr ? 'صلاحيات المستخدم التفصيلية' : 'Detailed User Permissions' }} - {{ $user->name }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index', app()->getLocale()) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form id="permissions-form" method="POST" action="{{ route('admin.users.permissions.update', [app()->getLocale(), $user->id]) }}">
    @csrf
    @method('PUT')

    <div class="d-flex gap-2 mb-3">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">{{ $isAr ? 'تحديد الكل' : 'Select All' }}</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">{{ $isAr ? 'إلغاء تحديد الكل' : 'Unselect All' }}</button>
    </div>

    <div id="empty-permissions-warning" class="alert alert-warning d-none">
        {{ $isAr ? 'لا يمكن حفظ المستخدم بدون أي صلاحية.' : 'Cannot save user without any permission.' }}
    </div>

    <div class="row g-3">
        @foreach($groupedPermissions as $group => $permissions)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong>{{ $groupTitles[$group] ?? $group }}</strong>
                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="toggleGroup('{{ $group }}')">
                            {{ $isAr ? 'تحديد القسم' : 'Toggle Group' }}
                        </button>
                    </div>
                    <div class="card-body">
                        @foreach($permissions as $permission)
                            @if($allPermissions->contains($permission))
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input perm-checkbox perm-group-{{ $group }}"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission }}"
                                        id="perm_{{ $permission }}"
                                        @checked(in_array($permission, $currentPermissions, true))
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
            </div>
        @endforeach
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-success">{{ $isAr ? 'حفظ الصلاحيات' : 'Save Permissions' }}</button>
    </div>
</form>

<script>
    function toggleAll(state) {
        document.querySelectorAll('.perm-checkbox').forEach(function (el) {
            el.checked = state;
        });
        validateCheckedPermissions();
    }

    function toggleGroup(group) {
        var items = document.querySelectorAll('.perm-group-' + group);
        if (!items.length) return;
        var allChecked = Array.from(items).every(function (el) {
            return el.checked;
        });
        items.forEach(function (el) {
            el.checked = !allChecked;
        });
        validateCheckedPermissions();
    }

    function validateCheckedPermissions() {
        var checkedCount = document.querySelectorAll('.perm-checkbox:checked').length;
        var warning = document.getElementById('empty-permissions-warning');
        if (warning) {
            warning.classList.toggle('d-none', checkedCount > 0);
        }
        return checkedCount > 0;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.perm-checkbox').forEach(function (el) {
            el.addEventListener('change', validateCheckedPermissions);
        });

        var form = document.getElementById('permissions-form');
        if (form) {
            form.addEventListener('submit', function (event) {
                if (!validateCheckedPermissions()) {
                    event.preventDefault();
                }
            });
        }

        validateCheckedPermissions();
    });
</script>
@endsection
