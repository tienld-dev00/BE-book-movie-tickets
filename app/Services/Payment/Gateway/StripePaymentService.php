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
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            $user = Auth::user();
            $orderId = $data['order_id'];
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
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $orderId
                ],
            ]);

            return [
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
}
