<?php

namespace App\Subscription\Infrastructure\Provider;

use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Infrastructure\Repositories\EloquentSubscriberRepository;
use App\Subscription\Infrastructure\Repositories\EloquentSubscriptionRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrations();
        $this->registerRoutes();
        $this->loadModulesRepositories();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/Subscription/Infrastructure/database/migrations')
        );
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfig(), function () {
            $this->loadRoutesFrom(
                base_path('src/Subscription/Infrastructure/routes/web.php')
            );
        });
    }

    private function routeConfig(): array
    {
        $defaultPrefix = '/subscriptions';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => ['auth:sanctum']
        ];
    }

    private function loadModulesRepositories(): void
    {
        $this->app->singleton(SubscriptionRepository::class, EloquentSubscriptionRepository::class);
        $this->app->singleton(SubscriberRepository::class, EloquentSubscriberRepository::class);
    }
}
