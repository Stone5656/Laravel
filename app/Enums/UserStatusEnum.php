<?php

namespace App\Enums;

enum UserStatusEnum: string
{
    case ACTIVE = 'active';
    case DELETED = 'deleted';
    case SUSPENDED = 'suspended';
}
