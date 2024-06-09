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
            return $this->paymentRepository->updateOrCreate($this->checkData, $this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }

    public function setParams($data = null)
    {
        $this->checkData = ['payment_intent_id' => $data['payment_intent_id']];
        $this->data = $data;

        return $this;
    }
}
