<?php

namespace App\Subscription\Infrastructure\Provider;

use App\Shared\Domain\Event\DomainEventPublisher;
use App\Subscription\Domain\Repository\SubscriberRepository;
use App\Subscription\Domain\Repository\SubscriptionRepository;
use App\Subscription\Domain\Services\SubscriptionService;
use App\Subscription\Domain\Subscribers\SubscriptionEventSubscriber;
use App\Subscription\Infrastructure\Repositories\EloquentSubscriberRepository;
use App\Subscription\Infrastructure\Repositories\EloquentSubscriptionRepository;
use App\Subscription\Infrastructure\Services\EloquentSubscriptionService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrations();
        $this->registerRoutes();
        $this->loadModulesRepositories();
        $this->loadServices();
        $this->setupSubscribers();
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

    private function setupSubscribers(): void
    {
        $domainEventPublisher = app(DomainEventPublisher::class);
        $domainEventPublisher->subscribe(
            new SubscriptionEventSubscriber(
                subscriptionRepository: app(SubscriptionRepository::class),
                subscriberRepository: app(SubscriberRepository::class)
            )
        );
    }

    private function loadServices(): void
    {
        $this->app->singleton(SubscriptionService::class, EloquentSubscriptionService::class);
    }
}
