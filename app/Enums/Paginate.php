<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Paginate extends Enum
{
    /**
     * Status default per page.
     * @var int
     */
    const DEFAULT = 10;
}
