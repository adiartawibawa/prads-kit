<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case EDITOR = 'Editor';
    case USER = 'User';
}
