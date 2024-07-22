<?php

namespace App\Statistics\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path(
                'src/Statistics/Infrastructure/database/migrations'
            )
        );
    }
}
