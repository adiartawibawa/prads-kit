<?php

namespace App\Policies;

use App\Enums\Permissions\UserPermission;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(UserPermission::VIEW_ANY->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->can(UserPermission::VIEW_ANY->value)) {
            return true;
        }

        return $user->can(UserPermission::VIEW->value)
            && $user->is($model);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(UserPermission::CREATE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->can(UserPermission::UPDATE_ANY->value)) {
            return true;
        }

        return $user->can(UserPermission::UPDATE->value)
            && $user->is($model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->can(UserPermission::DELETE_ANY->value)) {
            return true;
        }

        return $user->can(UserPermission::DELETE->value)
            && $user->is($model);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can(UserPermission::RESTORE->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can(UserPermission::FORCE_DELETE->value);
    }
}
