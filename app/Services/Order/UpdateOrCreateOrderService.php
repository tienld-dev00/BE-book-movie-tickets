<?php

namespace App\Services\Order;

use App\Interfaces\Order\OrderRepositoryInterface;
use App\Services\BaseService;
use App\Services\Ticket\CreateTicketsService;
use Exception;
use Illuminate\Support\Facades\Log;

class UpdateOrCreateOrderService extends BaseService
{
    protected $orderRepository;
    protected $checkData;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle()
    {
        try {
            return $this->orderRepository->updateOrCreate($this->checkData, $this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }

    public function setParams($data = null)
    {
        $this->checkData = ['id' => $data['id']];
        $this->data = $data;

        return $this;
    }
}
