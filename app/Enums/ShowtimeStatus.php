<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ShowtimeStatus extends Enum
{
     /**
      * Status representing that the showtime is visible.
      * @var int
      */
     const SHOW = 0;

     /**
      * Status representing that the showtime is hidden.
      * @var int
      */
     const HIDE = 1;
}
