<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\ProcessPaymentRequest;
use App\Services\Payment\Gateway\StripePaymentService;
use App\Services\Payment\PaymentProcessorService;

class PaymentController extends Controller
{
    /**
     * Process a Stripe payment
     *
     * @param ProcessPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processStripePayment(ProcessPaymentRequest $request)
    {
        $paymentProcessor = new PaymentProcessorService(new StripePaymentService);
        $result = $paymentProcessor->setParams($request->validated())->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.create_success'),
                'data' => [
                    'clientSecret' => $result['clientSecret'],
                    'customerOptions' => $result['customerOptions']
                ]
            ]);
        }

        return $this->responseErrors(__('messages.create_fail'));
    }
}
