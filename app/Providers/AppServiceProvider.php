<?php

namespace App\Providers;

<<<<<<< HEAD
=======
use App\Repositories\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
>>>>>>> 27bbbff (Create Multi Guard)
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
<<<<<<< HEAD
        //
=======
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

>>>>>>> 27bbbff (Create Multi Guard)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
