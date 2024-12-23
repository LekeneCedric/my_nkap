<?php

namespace App\Operation\Infrastructure\provider;

use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\Services\GetOperationUserService;
use App\Operation\Infrastructure\Repository\PdoOperationAccountRepository;
use App\Operation\Infrastructure\Services\EloquentGetOperationUserService;
use App\Operation\Infrastructure\Services\GeminiAIService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OperationServiceProvider extends ServiceProvider
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
        $this->app->singleton(OperationAccountRepository::class, PdoOperationAccountRepository::class);
        $this->app->singleton(AIService::class, GeminiAIService::class);
        $this->app->singleton(GetOperationUserService::class, EloquentGetOperationUserService::class);
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/Operation/Infrastructure/database/migrations')
        );
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfig(), function() {
           $this->loadRoutesFrom(base_path('src/Operation/Infrastructure/routes/web.php'));
        });
    }

    private function routeConfig(): array
    {
        $defaultPrefix = '/operation';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => ['auth:sanctum']
        ];
    }
}
