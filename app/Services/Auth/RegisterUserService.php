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

            // Tạo chuỗi ngẫu nhiên
            $randomString = Str::random(25);

            // Tạo thời gian hết hạn cho token
            $expires = Carbon::now()->addMinutes(EmailAuthenticationTime::TIME)->timestamp;

            // Tạo payload cho JWT với chuỗi ngẫu nhiên và thời gian hết hạn
            $payload = JWTFactory::customClaims([
                'random' => $randomString,
                'exp' => $expires,
            ])->make();

            // Tạo token JWT
            $token = JWTAuth::encode($payload)->get();

            $frontEnd = config('app.front_end_url');
            // dd($frontEnd);

            // Tạo URL xác minh email
            $urlVerify = $frontEnd . '/confirmed-account?expired=' . $expires . '&user_id=' . $user->id . '&signature=' . $token;

            // Debug URL xác minh email
            dd($urlVerify);

            // Gửi email xác minh
            Mail::to($user->email)->send(new VerifyMailRegister($user, $urlVerify));

            return $this->data;
        } catch (Exception $e) {
            Log::error('register user fail', ['memo' => $e->getMessage()]);

            return false;
        }
    }
}
