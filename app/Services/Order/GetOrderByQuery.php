<?php

namespace App\Services\Order;

use App\Interfaces\Order\OrderRepositoryInterface;
use App\Models\Order;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetOrderByQuery extends BaseService
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle()
    {
        try {
            $builder = Order::query();

            if (isset($this->data['user_id'])) {
                $this->orderRepository->applyFilter($builder, 'user_id', $this->data['user_id']);
            }

            if (isset($this->data['keyword'])) {
                $this->orderRepository->applySearch($builder, $this->data['keyword']);
            }

            $this->orderRepository->applySort($builder, 'updated_at', 'desc');

            return $builder->paginate(6);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
