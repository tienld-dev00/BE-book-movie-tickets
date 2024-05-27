<?php

namespace App\Interfaces\Payment;

interface PaymentProcessInterface
{
    public function payment(int $amount);
}
