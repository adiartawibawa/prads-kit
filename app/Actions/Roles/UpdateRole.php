<?php

namespace App\Actions\Roles;

use App\Enums\Permissions\RolePermission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateRole
{
    public function handle(User $actor, Role $target, array $data): Role
    {
        return DB::transaction(function () use ($actor, $target, $data) {
            $target->update(['name' => $data['name']]);

            if (array_key_exists('permissions', $data) && $actor->can(RolePermission::ASSIGN_PERMISSION->value)) {
                $target->syncPermissions($data['permissions']);
            }

            return $target->fresh();
        });
    }
}
