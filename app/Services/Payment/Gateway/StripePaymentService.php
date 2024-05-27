<?php

namespace App\Services\Payment\Gateway;

use App\Interfaces\Payment\PaymentProcessInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class StripePaymentService implements PaymentProcessInterface
{
    public function payment(int $amount)
    {
        try {
            dump('Stripe Payment' . $amount);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
