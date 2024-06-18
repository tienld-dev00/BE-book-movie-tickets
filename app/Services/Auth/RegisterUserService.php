<?php

namespace App\Services\Auth;

use App\Enums\EmailAuthenticationTime;
use App\Mail\Auth\VerifyMailRegister;
use App\Services\User\CreateUserService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RegisterUserService extends CreateUserService
{
    public function handle()
    {
        try {
            $user = parent::handle();

            $verificationUrl = URL::temporarySignedRoute(
                'verify_email',
                now()->addMinutes(EmailAuthenticationTime::TIME),
                ['id' => $user->id]
            );

            Mail::to($user->email)->send(new VerifyMailRegister($user, $verificationUrl));

            return $this->data;
        } catch (Exception $e) {
            Log::error('register user fail', ['memo' => $e->getMessage()]);

            return false;
        }
    }
}
