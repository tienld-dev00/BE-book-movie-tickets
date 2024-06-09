<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    /**
     * The order is pending and awaiting further actions.
     * @var int
     */
    const PAYMENT_INCOMPLETE = 1;

    /**
     * The payment for the order has succeeded.
     * @var int
     */
    const PAYMENT_SUCCEEDED = 2;

    /**
     * The payment for the order has failed.
     * @var int
     */
    const PAYMENT_FAILED = 3;

    /**
     * The order has been refunded.
     * @var int
     */
    const REFUNDED = 4;
}
