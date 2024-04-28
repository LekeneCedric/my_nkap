<?php

namespace App\Bootstrap\Infrastructure\Providers;

use App\Account\Infrastructure\Provider\AccountServiceProvider;
use App\FinancialGoal\Infrastructure\Provider\FinancialGoalServiceProvider;
use App\Operation\Infrastructure\provider\OperationServiceProvider;
use App\Profession\Infrastructure\Provider\ProfessionServiceProvider;
use App\User\Infrastructure\Provider\UserProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->loadModuleServiceProviders();
        $this->loadDefaultMigrations();
        $this->loadDefaultView();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bindModuleRepositories();
        $this->bindSharedLibrairies();
    }

    /**
     * @return void
     */
    private function loadModuleServiceProviders(): void
    {
        $this->app->register(AccountServiceProvider::class);
        $this->app->register(OperationServiceProvider::class);
        $this->app->register(UserProvider::class);
        $this->app->register(FinancialGoalServiceProvider::class);
        $this->app->register(ProfessionServiceProvider::class);
    }

    /**
     * @return void
     */
    private function loadDefaultMigrations(): void
    {
        $this->loadMigrationsFrom('/src/Bootstrap/Infrastructure/database/migrations');
    }

    /**
     * @return void
     */
    private function loadDefaultView()
    {
        //
    }

    /**
     * @return void
     */
    private function bindModuleRepositories()
    {
        //
    }

    /**
     * @return void
     */
    private function bindSharedLibrairies()
    {
        //
    }
}
