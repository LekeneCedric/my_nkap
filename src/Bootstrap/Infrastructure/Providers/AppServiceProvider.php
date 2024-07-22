<?php

namespace App\Bootstrap\Infrastructure\Providers;

use App\Account\Infrastructure\Provider\AccountServiceProvider;
use App\category\Infrastructure\Provider\CategoryServiceProvider;
use App\FinancialGoal\Infrastructure\Provider\FinancialGoalServiceProvider;
use App\Operation\Infrastructure\provider\OperationServiceProvider;
use App\Profession\Infrastructure\Provider\ProfessionServiceProvider;
use App\Shared\Infrastructure\Logs\Provider\LogServiceProvider;
use App\Statistics\Infrastructure\Providers\StatisticsServiceProvider;
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
        $this->app->register(LogServiceProvider::class);
        $this->app->register(CategoryServiceProvider::class);
        $this->app->register(StatisticsServiceProvider::class);
    }

    /**
     * @return void
     */
    private function loadDefaultMigrations(): void
    {
        $this->loadMigrationsFrom('/src/Bootstrap/Infrastructure/database/migrations');
    }
}
