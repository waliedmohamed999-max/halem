<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'access-admin-dashboard',
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
            'manage-appointments',
            'manage-finance',
            'manage-messages',
            'manage-subscribers',
            'manage-settings',
            'manage-users',
            'manage-patient-records',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'Super Admin']);
        $contentManager = Role::query()->firstOrCreate(['name' => 'Content Manager']);
        $receptionist = Role::query()->firstOrCreate(['name' => 'Receptionist']);

        $superAdmin->syncPermissions($permissions);
        $contentManager->syncPermissions([
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
        ]);

        $receptionist->syncPermissions([
            'manage-appointments',
            'manage-finance',
            'manage-messages',
            'manage-patient-records',
            'manage-career-applications',
            'manage-subscribers',
        ]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@drhalim.local'],
            ['name' => 'Super Admin', 'password' => Hash::make('Admin@123456')]
        );

        $admin->syncRoles(['Super Admin']);
    }
}
