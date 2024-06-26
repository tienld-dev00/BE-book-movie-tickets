<?php

namespace App\Services\Auth;

use App\Enums\EmailAuthenticationTime;
use App\Mail\Auth\VerifyMailRegister;
use App\Services\User\CreateUserService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RegisterUserService extends CreateUserService
{
    public function handle()
    {
        try {
            $user = parent::handle();

            $randomString = Str::random(25);

            $expires = Carbon::now()->addMinutes(config('auth.email_authentication_time'))->timestamp;

            $payload = JWTFactory::customClaims([
                'sub' => $user->id,
                'exp' => $expires,
                'random' => $randomString,
            ])->make();

            $token = JWTAuth::encode($payload)->get();

            $frontEnd = config('app.front_end_url');

            $urlVerify = $frontEnd . '/confirmed-account?expired=' . $expires . '&user_id=' . $user->id . '&signature=' . $token;

            Mail::to($user->email)->send(new VerifyMailRegister($user, $urlVerify));

            return $this->data;
        } catch (Exception $e) {
            Log::error('register user fail', ['memo' => $e->getMessage()]);

            return false;
        }
    }
}
