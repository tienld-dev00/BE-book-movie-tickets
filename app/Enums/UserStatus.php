<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;


final class UserStatus extends Enum
{
    const ACTIVE = 0;
    const LOCK = 1;
}
