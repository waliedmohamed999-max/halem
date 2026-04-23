<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles')->latest();

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        if ($request->filled('role')) {
            $query->role((string) $request->input('role'));
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->get(['name']);

        $allPermissions = Permission::query()->orderBy('name')->pluck('name')->values();
        $groupedPermissions = $this->groupedPermissions();
        $userDirectPermissions = [];

        return view('admin.users.create', compact('roles', 'allPermissions', 'groupedPermissions', 'userDirectPermissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'allow_dashboard_access' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::query()->where('name', (string) $data['role'])->firstOrFail();
        $allowDashboardAccess = $request->boolean('allow_dashboard_access');
        $directPermissions = array_values($data['permissions'] ?? []);
        unset($data['role'], $data['allow_dashboard_access'], $data['permissions']);

        DB::transaction(function () use ($data, $role, $allowDashboardAccess, $directPermissions): void {
            $user = User::create($data);
            $user->syncRoles([$role->name]);
            $user->syncPermissions($directPermissions);

            if ($allowDashboardAccess || $role->name === 'Super Admin') {
                $user->givePermissionTo('access-admin-dashboard');
            } else {
                $user->revokePermissionTo('access-admin-dashboard');
            }

            if ($user->getAllPermissions()->isEmpty()) {
                throw ValidationException::withMessages([
                    'permissions' => app()->getLocale() === 'ar'
                        ? 'يجب منح المستخدم صلاحية واحدة على الأقل.'
                        : 'User must have at least one permission.',
                ]);
            }
        });

        return redirect()->route('admin.users.index', app()->getLocale())->with('success', 'Saved successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::query()
            ->withCount('permissions')
            ->orderBy('name')
            ->get(['name']);
        $allPermissions = Permission::query()->orderBy('name')->pluck('name')->values();
        $groupedPermissions = $this->groupedPermissions();
        $userDirectPermissions = $user->getDirectPermissions()->pluck('name')->all();

        return view('admin.users.edit', compact('user', 'roles', 'allPermissions', 'groupedPermissions', 'userDirectPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'allow_dashboard_access' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::query()->where('name', (string) $data['role'])->firstOrFail();
        $allowDashboardAccess = $request->boolean('allow_dashboard_access');
        $directPermissions = array_values($data['permissions'] ?? []);
        unset($data['role'], $data['allow_dashboard_access'], $data['permissions']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        DB::transaction(function () use ($user, $data, $role, $allowDashboardAccess, $directPermissions): void {
            $user->update($data);
            $user->syncRoles([$role->name]);
            $user->syncPermissions($directPermissions);

            if ($allowDashboardAccess || $role->name === 'Super Admin') {
                $user->givePermissionTo('access-admin-dashboard');
            } else {
                $user->revokePermissionTo('access-admin-dashboard');
            }

            if ($user->getAllPermissions()->isEmpty()) {
                throw ValidationException::withMessages([
                    'permissions' => app()->getLocale() === 'ar'
                        ? 'يجب منح المستخدم صلاحية واحدة على الأقل.'
                        : 'User must have at least one permission.',
                ]);
            }
        });

        return redirect()->route('admin.users.index', app()->getLocale())->with('success', 'Updated successfully');
    }

    public function destroy(User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('success', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users.index', app()->getLocale())->with('success', 'Deleted successfully');
    }

    public function editPermissions(User $user)
    {
        $allPermissions = Permission::query()->orderBy('name')->pluck('name')->values();
        $groupedPermissions = $this->groupedPermissions();

        return view('admin.users.permissions', compact('user', 'allPermissions', 'groupedPermissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissions = array_values($data['permissions'] ?? []);
        DB::transaction(function () use ($user, $permissions): void {
            $user->syncPermissions($permissions);

            if ($user->getAllPermissions()->isEmpty()) {
                throw ValidationException::withMessages([
                    'permissions' => app()->getLocale() === 'ar'
                        ? 'لا يمكن حفظ مستخدم بدون أي صلاحية.'
                        : 'Cannot save a user without any permission.',
                ]);
            }
        });

        return redirect()
            ->route('admin.users.permissions.edit', [app()->getLocale(), $user->id])
            ->with('success', 'Permissions updated successfully');
    }

    public function permissionsMatrix()
    {
        $permissions = Permission::query()->orderBy('name')->get();
        $users = User::query()
            ->with(['roles.permissions:name', 'permissions:name'])
            ->orderBy('name')
            ->get();

        return view('admin.users.permissions-matrix', compact('users', 'permissions'));
    }

    public function updatePermissionsMatrix(Request $request)
    {
        $permissions = Permission::query()->pluck('name')->all();
        $permissionLookup = array_fill_keys($permissions, true);
        $users = User::query()->with(['roles.permissions:name'])->get()->keyBy('id');
        $matrix = $request->input('matrix', []);
        $errors = [];

        DB::transaction(function () use ($matrix, $users, $permissionLookup, &$errors): void {
            foreach ($users as $userId => $user) {
                $userMatrix = $matrix[$userId] ?? [];
                $selected = [];

                foreach ($userMatrix as $permissionName => $enabled) {
                    if (!isset($permissionLookup[$permissionName])) {
                        continue;
                    }
                    if ((string) $enabled === '1') {
                        $selected[] = $permissionName;
                    }
                }

                $rolePermissions = $user->roles
                    ->flatMap(fn ($role) => $role->permissions->pluck('name'))
                    ->unique()
                    ->values()
                    ->all();

                if (count(array_unique(array_merge($selected, $rolePermissions))) === 0) {
                    $errors[] = $user->name;
                    continue;
                }

                $user->syncPermissions($selected);
            }

            if (!empty($errors)) {
                throw ValidationException::withMessages([
                    'matrix' => app()->getLocale() === 'ar'
                        ? 'لا يمكن حفظ بعض المستخدمين بدون صلاحيات: ' . implode('، ', $errors)
                        : 'Cannot save some users without permissions: ' . implode(', ', $errors),
                ]);
            }
        });

        return redirect()
            ->route('admin.users.permissions.matrix', app()->getLocale())
            ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث مصفوفة الصلاحيات بنجاح' : 'Permissions matrix updated successfully');
    }

    private function groupedPermissions(): array
    {
        return [
            'dashboard' => ['access-admin-dashboard'],
            'content' => [
                'manage-content',
                'manage-home-sections',
                'manage-marketing-sections',
                'manage-branches',
                'manage-working-hours',
                'manage-services',
                'manage-doctors',
                'manage-blog',
                'manage-pages',
                'manage-faqs',
                'manage-testimonials',
                'manage-careers',
                'manage-career-applications',
            ],
            'operations' => [
                'manage-appointments',
                'manage-finance',
                'manage-messages',
                'manage-subscribers',
                'manage-patient-records',
            ],
            'administration' => [
                'manage-users',
                'manage-settings',
            ],
        ];
    }
}
