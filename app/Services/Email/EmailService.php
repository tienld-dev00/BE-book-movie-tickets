<?php

namespace App\Services\Email;

use App\Interfaces\Email\EmailServiceInterface;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService implements EmailServiceInterface
{
    public function send(User $user)
    {
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}
