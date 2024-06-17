<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentMethod extends Enum
{
    /**
     * Payment method using Stripe.
     * @var int
     */
    const STRIPE = 1;
}
