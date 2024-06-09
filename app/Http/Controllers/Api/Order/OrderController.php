<?php

namespace App\Http\Controllers\Api\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Requests\Api\Order\GetOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\CreateOrderService;
use App\Services\Showtime\FindShowtimeService;
use App\Services\Ticket\CreateTicketsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Create order
     * 
     * @param CreateOrderRequest $request
     * 
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        $data = $request->validated();
        $orderId = $data['order_id'];
        $seats = $data['seats'];
        $tickets = [];
        $now = Carbon::now();

        $showtime = resolve(FindShowtimeService::class)->setParams($data['showtime_id'])->handle();

        /** Update or Create order */
        $order = resolve(CreateOrderService::class)->setParams([
            'id' => $orderId,
            'showtime_id' => $showtime->id,
            'status' => OrderStatus::PAYMENT_INCOMPLETE,
            'payment_method' => PaymentMethod::STRIPE,
            'user_id' => Auth::id()
        ])->handle();

        if ($order) {
            /** Create tickets data*/
            foreach ($seats as $seatId) {
                array_push($tickets, [
                    'seat_id' => $seatId,
                    'price' => $showtime->price,
                    'order_id' => $orderId,
                    "created_at" => $now,
                    "updated_at" => $now,
                ]);
            }

            /** Create tickets */
            $tickets = resolve(CreateTicketsService::class)->setParams($tickets)->handle();
        }

        if ($tickets) {
            return $this->responseSuccess([
                'message' => __('messages.create_success'),
                'data' => new OrderResource($order)
            ]);
        }

        return $this->responseErrors(__('messages.create_fail'));
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
