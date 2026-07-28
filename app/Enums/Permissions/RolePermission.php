<?php

namespace App\Enums\Permissions;

enum RolePermission: string
{
    case VIEW = 'role.view';
    case CREATE = 'role.create';
    case UPDATE = 'role.update';
    case DELETE = 'role.delete';

}
