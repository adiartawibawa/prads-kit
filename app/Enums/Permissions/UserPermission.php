<?php

namespace App\Enums\Permissions;

enum UserPermission: string
{
    case VIEW = 'user.view';
    case VIEW_ANY = 'user.view-any';
    case CREATE = 'user.create';
    case UPDATE = 'user.update';
    case UPDATE_ANY = 'user.update-any';
    case DELETE = 'user.delete';
    case DELETE_ANY = 'user.delete-any';
    case ASSIGN_ROLE = 'users.assign-role';
    case RESTORE = 'users.restore';
    case FORCE_DELETE = 'users.force-delete';

}
