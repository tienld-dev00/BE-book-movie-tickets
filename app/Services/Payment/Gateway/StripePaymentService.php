<?php

namespace App\Services\Payment\Gateway;

use App\Interfaces\Payment\PaymentGatewayInterface;
use App\Services\Showtime\FindShowtimeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Stripe;
use Exception;

class StripePaymentService implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    /**
     * process payment
     * 
     * @param array $data
     * 
     * @return boolean|array
     */
    public function processPayment(array $data)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

            $orderId = hexdec(uniqid());
            $user = Auth::user();
            $seats = $data['seats'];
            $showtime = resolve(FindShowtimeService::class)->setParams($data['showtime_id'])->handle();
            $amount = count($seats) * $showtime->price;

            /** create customer id if first time payment */
            if (is_null($user['stripe_id'])) {
                $customer = $stripe->customers->create([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);

                $user->stripe_id = $customer->id;
                $user->save();
            }

            $ephemeralKey = \Stripe\EphemeralKey::create(
                ['customer' => $user->stripe_id],
                ['stripe_version' => '2024-04-10']
            );

            $intent = $stripe->paymentIntents->create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $orderId,
                    'user_id' => Auth::id(),
                    'showtime_id' => $showtime->id,
                    'tickets' => json_encode(array_map(function ($seatId) use ($showtime, $orderId) {
                        return ['seat_id' => $seatId, 'price' => $showtime->price, 'order_id' => $orderId];
                    }, $data['seats']))
                ],
            ]);

            return [
                'orderId' => $orderId,
                'clientSecret' => $intent->client_secret,
                'customerOptions' => [
                    'customer' => $user->stripe_id,
                    'ephemeralKey' => $ephemeralKey->secret,
                ],
            ];
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }

    /**
     * refund payment
     * 
     * @param string $paymentIntentId
     * 
     * @return boolean|object
     */
    public function refundPayment(string $paymentIntentId)
    {
        try {
            $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

            return $stripe->refunds->create(['payment_intent' => $paymentIntentId, 'metadata' => ['hello' => '123']]);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
