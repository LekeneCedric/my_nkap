<?php

namespace App\category\Infrastructure\Provider;

use App\category\Domain\UserCategoryRepository;
use App\category\Infrastructure\Repository\EloquentUserCategoryRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bindModulesRepositories();
    }
    public function register(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/category/Infrastructure/database/migrations')
        );
    }

    private function loadRoutes(): void
    {
        Route::group($this->routeConfig(), function() {
            $this->loadRoutesFrom(
                base_path('src/category/Infrastructure/routes/web.php')
            );
        });
    }

    private function routeConfig(): array
    {
        $defaultPrefix = '/category';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => ['auth:sanctum']
        ];
    }

    private function bindModulesRepositories(): void
    {
        $this->app->singleton(UserCategoryRepository::class, EloquentUserCategoryRepository::class);
    }
}
