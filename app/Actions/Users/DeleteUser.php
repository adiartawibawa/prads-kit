<?php

// app/Actions/Users/DeleteUser.php

namespace App\Actions\Users;

use App\Models\User;

class DeleteUser
{
    public function handle(User $actor, User $target): void
    {
        // cegah user hapus akun sendiri lewat CRUD admin
        abort_if($actor->is($target), 422, 'Tidak bisa menghapus akun sendiri.');

        $target->delete(); // soft delete, karena model pakai SoftDeletes
    }
}
