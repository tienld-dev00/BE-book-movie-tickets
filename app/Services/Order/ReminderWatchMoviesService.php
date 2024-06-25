<?php

namespace App\Services\Order;

use App\Interfaces\Order\OrderRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ReminderWatchMoviesService extends BaseService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle()
    {
        try {
            $orders =  $this->orderRepository->orderHaveShowtimes30min();

            return $orders;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
