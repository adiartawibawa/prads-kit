<?php

namespace App\Actions\Users;

use App\Models\User;

class RestoreUser
{
    public function handle(User $user): User
    {
        $user->restore();

        return $user->fresh();
    }
}
