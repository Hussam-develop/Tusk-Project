<?php

namespace App\Providers;

<<<<<<< HEAD
=======
>>>>>>> 27bbbff (Create Multi Guard)
namespace App\Providers;

use App\Models\Accountant;
use App\Models\Admin;
use App\Models\Dentist;
use App\Models\InventoryEmployee;
use App\Models\LabManager;
use App\Models\Patient;
use App\Models\Secretary;
use App\Repositories\AuthRepository;
use App\Repositories\AuthRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
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
          Relation::morphMap([
            'accountant' => Accountant::class,
            'admin' => Admin::class,
            'dentist' => Dentist::class,
            'inventoryEmployee' => InventoryEmployee::class,
            'labManager' => LabManager::class,
            'patient' => Patient::class,
            'secretary' => Secretary::class,
        ]);
    }
}
