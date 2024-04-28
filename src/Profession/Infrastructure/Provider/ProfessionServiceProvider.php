<?php

namespace App\Profession\Infrastructure\Provider;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ProfessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfig(), function() {
            $this->loadRoutesFrom(
                base_path('src/Profession/Infrastructure/routes/web.php')
            );
        });
    }

    private function routeConfig(): array
    {
        return [
            'prefix' => 'api/professions',
            'middleware' => [],
        ];
    }
}
