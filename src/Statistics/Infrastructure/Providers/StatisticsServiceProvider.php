<?php

namespace App\Statistics\Infrastructure\Providers;

use App\Statistics\Domain\repositories\MonthlyStatisticRepository;
use App\Statistics\Infrastructure\Repositories\EloquentMonthlyStatisticRepository;
use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
        $this->bindModuleRepositories();
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
    }
}
