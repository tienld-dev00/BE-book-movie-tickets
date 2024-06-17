<?php

namespace App\Interfaces\Payment;

interface PaymentGatewayInterface
{
    public function processPayment(array $data);

    public function refundPayment(string $transactionId);
}
