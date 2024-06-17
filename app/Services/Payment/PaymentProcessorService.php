<?php

namespace App\Services\Payment;

use App\Interfaces\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use App\Services\BaseService;
use Exception;

class PaymentProcessorService extends BaseService
{
    protected $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function handle()
    {
        try {
            return $this->paymentGateway->processPayment($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
