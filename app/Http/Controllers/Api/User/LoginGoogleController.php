<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class LoginGoogleController extends Controller
{
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

    public function loginGoogleCallback(Request $request)
    {
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();
            if ($user) {

                $token = auth()->login($user);
                // localStorage.setItem("token", accessToken);
                session(['token' => $token]);

                $user->update(['google_id' => $googleUser->id]);
            } else {
                $user = User::create(
                    [
                        'email' => $googleUser->email,
                        'name' => $googleUser->name,
                        'google_id' => $googleUser->id,
                        'password' => Str::random(10),
                    ]
                );

                $token = auth()->login($user);
            }

            $queryParams = http_build_query([
                'status' => __('users.login_google_success'),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
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
