<?php

namespace App\category\Infrastructure\Provider;

use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadMigrations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/category/Infrastructure/database/migrations')
        );
    }
}
