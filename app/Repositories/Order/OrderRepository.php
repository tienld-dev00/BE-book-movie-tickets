<?php

namespace App\Repositories\Order;

use App\Interfaces\Order\OrderRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $order)
    {
        $this->model = $order;
    }
}
