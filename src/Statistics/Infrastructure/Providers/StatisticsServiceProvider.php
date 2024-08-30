<?php

namespace App\Statistics\Infrastructure\Providers;

use App\Shared\Domain\Event\DomainEventPublisher;
use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;
use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Domain\Subscribers\StatisticsEventSubscriber;
use App\Statistics\Infrastructure\Repositories\EloquentMonthlyCategoryStatisticRepository;
use App\Statistics\Infrastructure\Repositories\EloquentMonthlyStatisticRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
        $this->bindModuleRepositories();
        $this->registerRoutes();
    }

    public function boot(): void
    {
        $domainEventPublisher = app(DomainEventPublisher::class);

        $domainEventPublisher->subscribe(
            new StatisticsEventSubscriber(
                monthlyStatisticRepository: app(MonthlyStatisticRepository::class),
                monthlyCategoryStatisticRepository: app(MonthlyCategoryStatisticRepository::class),
            ),
        );
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path(
                'src/Statistics/Infrastructure/database/migrations'
            )
        );
    }

    private function bindModuleRepositories(): void
    {
        $this->app->singleton(MonthlyStatisticRepository::class, EloquentMonthlyStatisticRepository::class);
        $this->app->singleton(MonthlyCategoryStatisticRepository::class, EloquentMonthlyCategoryStatisticRepository::class);
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfig(), function(){
            $this->loadRoutesFrom(
                base_path('src/Statistics/Infrastructure/routes/web.php')
            );
        });
    }

    private function routeConfig(): array
    {
        $defaultPrefix = '/statistics';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => ['auth:sanctum']
        ];
    }
}
