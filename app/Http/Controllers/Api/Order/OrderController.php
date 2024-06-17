<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\GetOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\GetOrderByQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Get orders
     * 
     * @return Response
     */
    public function index()
    {
        $result = resolve(GetOrderByQuery::class)->setParams(['user_id' => Auth::id()])->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.get_success'),
                'data' => $result
            ]);
        }

        return $this->responseErrors(__('messages.get_fail'));
    }

    /**
     * Get order info
     * 
     * @param GetOrderRequest $request
     * @param Order $order
     * 
     * @return Response
     */
    public function show(GetOrderRequest $request, Order $order)
    {
        return $this->responseSuccess([
            'message' => __('messages.get_success'),
            'data' => new OrderResource($order)
        ]);
    }
}
