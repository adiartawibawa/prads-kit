<?php

namespace App\Actions\Roles;

use App\Models\Role;

class DeleteRole
{
    public function handle(Role $target): void
    {
        $target->permissions()->detach();
        $target->delete();
    }
}
