<?php

namespace App\Actions\Users;

use App\Models\User;

class ForceDeleteUser
{
    public function handle(User $actor, User $target): void
    {
        abort_if($actor->is($target), 422, 'Tidak bisa menghapus permanen akun sendiri.');

        $target->forceDelete();
    }
}
