<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class LoginFacebookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function facebookSignInUrl()
    {
        try {
            $url = Socialite::driver('facebook')->stateless()
                ->redirect()->getTargetUrl();
            return response()->json([
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
    public function loginFacebookCallback(Request $request)
    {
        try {
            $state = $request->input('state');

            parse_str($state, $result);
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::where('email', $facebookUser->email)->first();
            if ($user) {

                $token = auth()->login($user);

                $user->update(['facebook_id' => $facebookUser->id]);

                return response()->json([
                    'status' => __('users.facebook_sign_in_email_existed'),
                    'data' => [
                        'user' => $user,
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60,
                    ],
                ], Response::HTTP_OK);
            }
            $user = User::create(
                [
                    'email' => $facebookUser->email,
                    'name' => $facebookUser->name,
                    'facebook_id' => $facebookUser->id,
                    'password' => Str::random(10),
                ]
            );

            $token = auth()->login($user);

            return response()->json([
                'status' => __('users.login_facebook_success'),
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                ],
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {

            return response()->json([
                'status' => __('users.login_facebook_failed'),
                'error' => $exception,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
