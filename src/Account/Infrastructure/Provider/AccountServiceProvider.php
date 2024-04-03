<?php

namespace App\Account\Infrastructure\Provider;

use App\Account\Domain\Repository\AccountRepository;
use App\Account\Infrastructure\Repository\PdoAccountRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bindModulesRepositories();
    }
    public function register(): void
    {
        $this->loadMigrations();
        $this->registerRoutes();
    }

    private function bindModulesRepositories(): void
    {
        $this->app->singleton(AccountRepository::class, PdoAccountRepository::class);
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('/src/Account/Infrastructure/database/migrations')
        );
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfig(), function () {
           $this->loadRoutesFrom(
               base_path('/src/Account/Infrastructure/routes/web.php')
           );
        });
    }

    private function routeConfig(): array
    {
        $defaultPrefix = '/accounts';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => ['auth:sanctum']
        ];
    }
}
