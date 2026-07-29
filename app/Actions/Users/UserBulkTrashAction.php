<?php

namespace App\Actions\Users;

use App\Actions\BulkTrashAction;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserBulkTrashAction extends BulkTrashAction
{
    protected function query(): Builder
    {
        return User::onlyTrashed();
    }

    protected function forceDelete(
        Authenticatable $actor,
        Collection $users
    ): int {
        $users = $users->reject(fn (User $user) => $actor->is($user));

        return parent::forceDelete($actor, $users);
    }
}
