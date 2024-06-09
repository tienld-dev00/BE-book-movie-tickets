<?php

namespace App\Services\Payment;

use App\Interfaces\Payment\PaymentRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class SavePaymentResultService extends BaseService
{
    protected $paymentRepository;
    protected $checkData;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function handle()
    {
        try {
            return $this->paymentRepository->create($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
