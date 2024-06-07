<?php

namespace App\Providers;

use App\Interfaces\Email\EmailServiceInterface;
use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Showtime\ShowtimeRepository;
use App\Repositories\User\UserRepository;
use App\Services\Email\EmailService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EmailServiceInterface::class, EmailService::class);
        $this->app->bind(ShowtimeRepositoryInterface::class, ShowtimeRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
