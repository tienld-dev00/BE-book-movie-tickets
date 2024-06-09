<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Services\Firebase\FirestoreService;
use App\Services\Payment\SavePaymentResultService;
use App\Services\Order\UpdateOrderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use Stripe\Webhook;
use Illuminate\Http\Response;

class StripeWebhookController extends Controller
{
    /**
     * Handle all stripe webhook
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return $this->responseErrors('Invalid payload');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return $this->responseErrors('Invalid signature');
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
            default:
                Log::info('Received unknown event type', ['event' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle payment intent succeeded
     * 
     * @param object $paymentIntent
     * 
     * @return void
     */
    protected function handlePaymentIntentSucceeded(object $paymentIntent)
    {
        Log::info('PaymentIntent was successful!', ['payment_intent' => $paymentIntent]);

        $order = resolve(UpdateOrderService::class)
            ->setParams([
                'id' => $paymentIntent->metadata->order_id,
                'status' => OrderStatus::PAYMENT_SUCCEEDED
            ])
            ->handle();

        /** Update seats's status in firestore to true - seat have been ordered */
        if ($order) {
            $firestore = FirestoreService::connect();

            $collectionReference = $firestore->collection('seats');
            $data = $collectionReference
                ->where('user_id', '=', $order->user_id)
                ->where('showtime_id', '=', $order->showtime_id)
                ->where('status', '=', false) /** Haven't ordered yet */
                ->documents();

            foreach ($data as $value) {
                if ($value->exists()) {
                    $value->reference()->update([['path' => 'status', 'value' => true]]);
                }
            }
        }

        resolve(SavePaymentResultService::class)
            ->setParams([
                'order_id' => $paymentIntent->metadata->order_id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'payment_method_id' => $paymentIntent->payment_method,
            ])
            ->handle();
    }

    /**
     * Handle payment intent failed
     * 
     * @param object $paymentIntent
     * 
     * @return void
     */
    protected function handlePaymentIntentFailed(object $paymentIntent)
    {
        Log::info('PaymentIntent was failed!', ['payment_intent' => $paymentIntent]);

        resolve(UpdateOrderService::class)
            ->setParams([
                'id' => $paymentIntent->metadata->order_id,
                'status' => OrderStatus::PAYMENT_FAILED
            ])
            ->handle();

        resolve(SavePaymentResultService::class)
            ->setParams([
                'order_id' => $paymentIntent->metadata->order_id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'payment_method_id' => $paymentIntent->last_payment_error->payment_method->id,
            ])
            ->handle();
    }
}
