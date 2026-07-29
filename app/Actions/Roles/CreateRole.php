<?php

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateRole
{
    public function handle(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard'],
            ]);

            $role->syncPermissions($data['permissions']);

            return $role;
        });
    }
}
