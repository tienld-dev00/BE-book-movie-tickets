<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Enums\PaymentMethod;
use App\Jobs\SendEmail\Order\SendMailOrderSuccessJob;
use App\Services\Firebase\FirestoreService;
use App\Services\Order\UpdateOrCreateOrderService;
use App\Services\Payment\SavePaymentResultService;
use App\Http\Controllers\Controller;
use App\Services\Ticket\CreateTicketsService;
use Google\Cloud\Firestore\Filter;
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
                return $this->handlePaymentIntentSucceeded($paymentIntent);
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                return $this->handlePaymentIntentFailed($paymentIntent);
            case 'charge.refunded':
                $paymentIntent = $event->data->object;
                return $this->handlePaymentIntentRefunded($paymentIntent);
            default:
                Log::info('Received unknown event type', ['event' => $event->type]);
        }

        return $this->responseSuccess(['message' => 'success']);
    }

    /**
     * Handle payment intent succeeded
     * 
     * @param object $paymentIntent
     * 
     * @return Response
     */
    protected function handlePaymentIntentSucceeded(object $paymentIntent)
    {
        Log::info('PaymentIntent was successful!', ['payment_intent' => $paymentIntent]);

        $order = resolve(UpdateOrCreateOrderService::class)
            ->setParams([
                'id' => $paymentIntent->metadata->order_id,
                'showtime_id' => (int) $paymentIntent->metadata->showtime_id,
                'payment_method' => PaymentMethod::STRIPE,
                'user_id' => (int) $paymentIntent->metadata->user_id,
                'status' => OrderStatus::PAYMENT_SUCCEEDED
            ])
            ->handle();

        if (!$order) {
            return $this->responseErrors('Error when update or create order');
        }

        if (empty($order->tickets->toArray())) { /** If the ticket has not been created yet */
            $tickets = resolve(CreateTicketsService::class)
                ->setParams(json_decode($paymentIntent->metadata->tickets, true))
                ->handle();

            if (!$tickets) {
                return $this->responseErrors('Error when create tickets');
            }
        }

        SendMailOrderSuccessJob::dispatch($order);

        /** Update seats's status in firestore to true - seat have been ordered */
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

        $payment = resolve(SavePaymentResultService::class)
            ->setParams([
                'order_id' => $paymentIntent->metadata->order_id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => 'payment succeeded',
                'payment_method_id' => $paymentIntent->payment_method,
            ])
            ->handle();

        if (!$payment) {
            return $this->responseErrors('Error when save payment result.');
        }

        return $this->responseSuccess(['message' => 'success']);
    }

    /**
     * Handle payment intent failed
     * 
     * @param object $paymentIntent
     * 
     * @return Response
     */
    protected function handlePaymentIntentFailed(object $paymentIntent)
    {
        Log::info('PaymentIntent was failed!', ['payment_intent' => $paymentIntent]);

        $order = resolve(UpdateOrCreateOrderService::class)
            ->setParams([
                'id' => $paymentIntent->metadata->order_id,
                'showtime_id' => (int) $paymentIntent->metadata->showtime_id,
                'payment_method' => PaymentMethod::STRIPE,
                'user_id' => (int) $paymentIntent->metadata->user_id,
                'status' => OrderStatus::PAYMENT_FAILED
            ])
            ->handle();

        if (!$order) {
            return $this->responseErrors('Error when update or create order');
        }

        if (empty($order->tickets->toArray())) { /** If the ticket has not been created yet */
            $tickets = resolve(CreateTicketsService::class)
                ->setParams(json_decode($paymentIntent->metadata->tickets, true))
                ->handle();

            if (!$tickets) {
                return $this->responseErrors('Error when create tickets');
            }
        }

        $payment = resolve(SavePaymentResultService::class)
            ->setParams([
                'order_id' => $paymentIntent->metadata->order_id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'payment_method_id' => $paymentIntent->last_payment_error->payment_method->id,
            ])
            ->handle();

        if (!$payment) {
            return $this->responseErrors('Error when save payment result.');
        }

        return $this->responseSuccess(['message' => 'success']);
    }

    /**
     * handle payment intent refunded
     * 
     * @param object $paymentIntent
     * 
     * @return Response
     */
    protected function handlePaymentIntentRefunded(object $paymentIntent)
    {
        Log::info('PaymentIntent Refunded!', ['payment_intent' => $paymentIntent]);

        $order = resolve(UpdateOrCreateOrderService::class)
            ->setParams([
                'id' => $paymentIntent->metadata->order_id,
                'status' => OrderStatus::REFUNDED
            ])
            ->handle();

        if (!$order) {
            return $this->responseErrors('Error when update order');
        }

        /** Delete seats in firestore  */
        $firestore = FirestoreService::connect();
        $collectionReference = $firestore->collection('seats');
        $data = $collectionReference
            ->where('user_id', '=', $order->user_id)
            ->where('showtime_id', '=', $order->showtime_id)
            ->where('status', '=', true) /** Have been ordered */
            ->where(Filter::or(array_map(function ($ticket) {
                return Filter::field('id', '=', $ticket['seat_id']);
            }, $order->tickets->toArray())))
            ->documents();

        foreach ($data as $value) {
            if ($value->exists()) {
                $value->reference()->delete();
            }
        }

        $payment = resolve(SavePaymentResultService::class)
            ->setParams([
                'order_id' => $paymentIntent->metadata->order_id,
                'payment_intent_id' => $paymentIntent->payment_intent,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'status' => 'refunded',
                'payment_method_id' => $paymentIntent->payment_method,
            ])
            ->handle();

        if (!$payment) {
            return $this->responseErrors('Error when save payment result.');
        }

        return $this->responseSuccess(['message' => 'success']);
    }
}
