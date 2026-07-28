<?php

// app/Actions/Users/UpdateUser.php

namespace App\Actions\Users;

use App\Enums\Permissions\UserPermission;
use App\Models\User;

class UpdateUser
{
    public function handle(User $actor, User $target, array $data): User
    {
        $target->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // hanya update role jika actor punya izin & role dikirim
        if (isset($data['role']) && $actor->can(UserPermission::ASSIGN_ROLE->value)) {
            $target->syncRoles([$data['role']]);
        }

        return $target->fresh();
    }
}
