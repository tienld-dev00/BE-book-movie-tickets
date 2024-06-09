<?php

namespace App\Repositories\Payment;

use App\Interfaces\Payment\PaymentRepositoryInterface;
use App\Models\Payment;
use App\Repositories\BaseRepository;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    /**
     * Update or create payment result
     * 
     * @param array $checkData
     * @param array $updateData
     * 
     * @return [type]
     */
    public function updateOrCreate(array $checkData, array $updateData)
    {
        $this->model->updateOrCreate($checkData, $updateData);
    }
}
