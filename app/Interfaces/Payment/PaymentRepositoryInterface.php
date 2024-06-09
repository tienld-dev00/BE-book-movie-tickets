<?php

namespace App\Interfaces\Payment;

use App\Interfaces\CrudRepositoryInterface;

interface PaymentRepositoryInterface extends CrudRepositoryInterface
{
    public function updateOrCreate(array $checkData, array $data);
}
