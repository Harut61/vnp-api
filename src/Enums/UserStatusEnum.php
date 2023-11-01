<?php

namespace App\Enums;

class UserStatusEnum extends BasicEnum
{
    const PENDING = 'pending';
    const ACTIVE = 'activated';
    const BLOCKED = 'blocked';
}