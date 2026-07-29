<?php

namespace Database\Seeders;

use App\Enums\Permissions\RolePermission;
use App\Enums\Permissions\UserPermission;
use App\Enums\Role as EnumsRole;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        foreach ([
            ...UserPermission::cases(),
            ...RolePermission::cases(),
        ] as $permission) {
            Permission::findOrCreate($permission->value);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $superAdmin = Role::findOrCreate(EnumsRole::SUPER_ADMIN->value);
        $admin = Role::findOrCreate(EnumsRole::ADMIN->value);
        $editor = Role::findOrCreate(EnumsRole::EDITOR->value);
        $user = Role::findOrCreate(EnumsRole::USER->value);

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions
        |--------------------------------------------------------------------------
        */
        $admin->givePermissionTo([
            UserPermission::VIEW_ANY->value,
            UserPermission::CREATE->value,
            UserPermission::UPDATE_ANY->value,
            UserPermission::DELETE_ANY->value,
            UserPermission::ASSIGN_ROLE->value,
            RolePermission::VIEW->value,
            RolePermission::ASSIGN_PERMISSION->value,
        ]);

        $editor->givePermissionTo([
            UserPermission::VIEW_ANY->value,
            UserPermission::UPDATE_ANY->value,
        ]);

        $user->givePermissionTo([
            UserPermission::VIEW->value,
            UserPermission::CREATE->value,
            UserPermission::UPDATE->value,
            UserPermission::DELETE->value,
        ]);

    }
}
