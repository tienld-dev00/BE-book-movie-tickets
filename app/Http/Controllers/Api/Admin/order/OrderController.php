<?php

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\GetOrderByQueryService;
use App\Services\Payment\Gateway\StripePaymentService;
use App\Services\Payment\RefundPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get order by query
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $result = resolve(GetOrderByQueryService::class)->setParams($query)->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.get_success'),
                'data' => OrderResource::collection($result),
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'from' => $result->firstItem(),
                    'last_page' => $result->lastPage(),
                    'path' => $result->path(),
                    'per_page' => $result->perPage(),
                    'to' => $result->lastItem(),
                    'total' => $result->total(),
                ],
            ]);
        }

        return $this->responseErrors(__('messages.get_fail'));
    }

    /**
     * Refund order
     * 
     * @param Request $request
     * @param Order $order
     * 
     * @return \Illuminate\Http\Response
     */
    public function refund(Request $request, Order $order)
    {
        $refundPayment = new RefundPaymentService(new StripePaymentService);
        $result = $refundPayment->setParams($order->payments[0]->payment_intent_id)->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.payment.refund_success'),
                'data' => new OrderResource($order)
            ]);
        }

        return $this->responseErrors(__('messages.payment.refund_fail'));
    }
}
