<?php

namespace App\FinancialGoal\Infrastructure\Provider;

use Illuminate\Support\ServiceProvider;

class FinancialGoalServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->loadMigrations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(base_path('src/FinancialGoal/Infrastructure/database/migrations'));
    }

}
