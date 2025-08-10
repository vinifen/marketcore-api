<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'CLIENT';
    case ADMIN = 'ADMIN';
    case MODERATOR = 'MODERATOR';
}
