<?php

namespace App\Providers;

use App\Interfaces\Email\EmailServiceInterface;
use App\Interfaces\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Interfaces\Categories\CategoriesRepositoryInterface;
use App\Repositories\Categories\CategoriesRepository;
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
        $this->app->bind(CategoriesRepositoryInterface::class, CategoriesRepository::class);
        $this->app->bind(EmailServiceInterface::class, EmailService::class);
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
