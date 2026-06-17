<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Every module supports: view, create, edit, delete, export, assign.
     */
    protected const MODULES = [
        'cities', 'coachings', 'branches', 'users', 'roles',
        'teachers', 'students', 'classes', 'subjects', 'batches',
        'homework', 'notices', 'study-materials', 'online-classes',
        'exams', 'results', 'chat', 'leads', 'subscriptions',
        'settings', 'reports', 'banners',
    ];

    protected const ACTIONS = ['view', 'create', 'edit', 'delete', 'export', 'assign'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::MODULES as $module) {
            foreach (self::ACTIONS as $action) {
                Permission::findOrCreate("{$module}.{$action}", 'web');
                Permission::where('name', "{$module}.{$action}")->update(['module' => $module]);
            }
        }

        $roles = [
            'Super Admin' => ['level' => 1, 'permissions' => '*'],
            'City Admin' => [
                'level' => 2,
                'permissions' => [
                    'coachings' => ['view', 'create', 'edit', 'export'],
                    'branches' => ['view', 'export'],
                    'users' => ['view', 'create', 'edit', 'export'],
                    'teachers' => ['view', 'export'],
                    'students' => ['view', 'export'],
                    'subscriptions' => ['view', 'export'],
                    'banners' => ['view', 'create', 'edit', 'delete'],
                    'reports' => ['view', 'export'],
                ],
            ],
            'Coaching Admin' => [
                'level' => 3,
                'permissions' => [
                    'banners' => ['view', 'create', 'edit', 'delete'],
                    'branches' => ['view', 'create', 'edit', 'delete', 'export'],
                    'users' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'teachers' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'students' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'classes' => ['view', 'create', 'edit', 'delete', 'export'],
                    'subjects' => ['view', 'create', 'edit', 'delete', 'export'],
                    'batches' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'homework' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'notices' => ['view', 'create', 'edit', 'delete', 'export'],
                    'study-materials' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'online-classes' => ['view', 'create', 'edit', 'delete', 'export'],
                    'exams' => ['view', 'create', 'edit', 'delete', 'export'],
                    'results' => ['view', 'create', 'edit', 'delete', 'export'],
                    'chat' => ['view', 'edit', 'delete'],
                    'leads' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'subscriptions' => ['view'],
                    'reports' => ['view', 'export'],
                ],
            ],
            'Branch Admin' => [
                'level' => 4,
                'permissions' => [
                    'users' => ['view', 'create', 'edit'],
                    'teachers' => ['view', 'create', 'edit', 'export'],
                    'students' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'classes' => ['view'],
                    'subjects' => ['view'],
                    'batches' => ['view', 'create', 'edit', 'export', 'assign'],
                    'homework' => ['view', 'create', 'edit', 'delete', 'export', 'assign'],
                    'notices' => ['view', 'create', 'edit', 'export'],
                    'study-materials' => ['view', 'create', 'edit', 'delete', 'export'],
                    'online-classes' => ['view', 'create', 'edit', 'delete', 'export'],
                    'exams' => ['view', 'create', 'edit', 'export'],
                    'results' => ['view', 'create', 'edit', 'export'],
                    'chat' => ['view'],
                    'leads' => ['view', 'create', 'edit', 'export'],
                    'reports' => ['view', 'export'],
                ],
            ],
            'Teacher' => [
                'level' => 5,
                'permissions' => [
                    'students' => ['view'],
                    'classes' => ['view'],
                    'subjects' => ['view'],
                    'batches' => ['view'],
                    'homework' => ['view', 'create', 'edit', 'delete', 'assign'],
                    'notices' => ['view'],
                    'study-materials' => ['view', 'create', 'edit'],
                    'online-classes' => ['view', 'create', 'edit'],
                    'exams' => ['view', 'create', 'edit'],
                    'results' => ['view', 'create', 'edit'],
                    'chat' => ['view'],
                ],
            ],
            'Student' => [
                'level' => 6,
                'permissions' => [
                    'homework' => ['view'],
                    'notices' => ['view'],
                    'study-materials' => ['view'],
                    'online-classes' => ['view'],
                    'results' => ['view'],
                ],
            ],
        ];

        foreach ($roles as $name => $definition) {
            /** @var Role $role */
            $role = Role::findOrCreate($name, 'web');
            $role->update(['level' => $definition['level']]);

            if ($definition['permissions'] === '*') {
                $role->syncPermissions(Permission::all());

                continue;
            }

            $names = [];
            foreach ($definition['permissions'] as $module => $actions) {
                foreach ($actions as $action) {
                    $names[] = "{$module}.{$action}";
                }
            }

            $role->syncPermissions($names);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
