<?php

namespace App\Services\Payment\Gateway;

use App\Interfaces\Payment\PaymentProcessInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class MomoPaymentService implements PaymentProcessInterface
{
    public function payment(int $amount)
    {
        try {
            dump('MOMO Payment' . $amount);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
