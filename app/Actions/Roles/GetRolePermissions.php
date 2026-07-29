<?php

namespace App\Actions\Roles;

use App\Enums\Permissions\RolePermission;
use App\Enums\Permissions\UserPermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GetRolePermissions
{
    public function handle(): Collection
    {
        return collect([
            ...UserPermission::cases(),
            ...RolePermission::cases(),
        ])
            ->groupBy(fn ($permission) => Str::before($permission->value, '.'))
            ->map(fn ($permissions, $group) => [
                'group' => str($group)->title()->value(),
                'permissions' => $permissions->map(fn ($permission) => [
                    'label' => Str::of(Str::after($permission->value, '.'))
                        ->replace('-', ' ')
                        ->title()
                        ->value(),
                    'value' => $permission->value,
                ])->values(),
            ])
            ->values();
    }
}
