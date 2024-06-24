<?php

namespace App\Services\Auth;

use App\Enums\EmailAuthenticationTime;
use App\Mail\Auth\VerifyMailRegister;
use App\Services\User\CreateUserService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterUserService extends CreateUserService
{
    public function handle()
    {
        try {
            $user = parent::handle();

            $urlVerify = 'http://localhost:3000/confirmed-account?expired=' . now()->addMinutes(EmailAuthenticationTime::TIME)->timestamp . '&user_id=' . $user->id;

            Mail::to($user->email)->send(new VerifyMailRegister($user, $urlVerify));

            return $this->data;
        } catch (Exception $e) {
            Log::error('register user fail', ['memo' => $e->getMessage()]);

            return false;
        }
    }
}
