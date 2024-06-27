<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Response;
use App\Models\User;



class LoginGoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function google()
    {
        try {
            $url = Socialite::driver('google')->stateless()
                ->redirect()->getTargetUrl();
            return $this->responseSuccess([
                'url' => $url,
            ])->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function loginGoogleCallback(Request $request)
    {
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();
            if ($user) {

                if ($user->status == 1) {
                    return $this->responseErrors([
                        'message' => __('auth.your_account_is_locked'),
                    ], Response::HTTP_CONFLICT);
                }

                $token = auth()->login($user);

                $user->update(['google_id' => $googleUser->id]);

                return $this->responseSuccess([
                    'status' => __('users.google_sign_in_email_existed'),
                    'data' => [
                        'user' => $user,
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60,
                    ],
                ], Response::HTTP_CREATED);
            }
            $user = User::create(
                [
                    'email' => $googleUser->email,
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                ]
            );

            $token = auth()->login($user);

            return $this->responseSuccess([
                'status' => __('users.login_google_success'),
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {

            return $this->responseErrors([
                'status' => __('users.login_google_failed'),
                'error' => $exception,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
