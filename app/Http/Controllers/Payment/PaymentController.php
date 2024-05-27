<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\Gateway\FincodePaymentService;
use App\Services\Payment\PaymentProcessorService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        // $paymentGateway = new PaymentProcessorService(new MomoPaymentService());
        $paymentGateway = new PaymentProcessorService(new FincodePaymentService());
        // $paymentGateway = new PaymentProcessorService(new StripePaymentService());
        $paymentGateway->setParams($request)->handle();

        return redirect()->route('users.index');
    }
}
