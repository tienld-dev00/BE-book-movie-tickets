<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class MovieStatus extends Enum
{
    /**
     * Status representing that the movie is visible.
     *
     * @var int
     */
    const SHOW = 0;

    /**
     * Status representing that the movie is hidden.
     *
     * @var int
     */
    const HIDE = 1;
}
