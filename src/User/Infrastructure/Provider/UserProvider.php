<?php

namespace App\User\Infrastructure\Provider;

use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\CheckIfAlreadyUserExistWithSameEmailByEmailService;
use App\User\Infrastructure\Repository\PdoUserRepository;
use App\User\Infrastructure\Services\PdoCheckIfAlreadyUserExistWithSameEmailByEmailService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserProvider extends  ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->loadMigrations();
        $this->registerRoutes();
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bindModuleRepositories();
        $this->bindModuleServices();
    }

    /**
     * @return void
     */
    private function bindModuleRepositories(): void
    {
        $this->app->singleton(UserRepository::class, PdoUserRepository::class);
    }

    private function bindModuleServices(): void
    {
        $this->app->singleton(CheckIfAlreadyUserExistWithSameEmailByEmailService::class,
            PdoCheckIfAlreadyUserExistWithSameEmailByEmailService::class);
    }

    /**
     * @return void
     */
    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            base_path('src/User/Infrastructure/database/migrations')
        );
    }

    /**
     * @return void
     */
    private function registerRoutes(): void
    {
        Route::group($this->getConfigs(), function () {
            $this->loadRoutesFrom(
                base_path('src/User/Infrastructure/routes/web.php')
            );
        });
    }

    /**
     * @return array
     */
    private function getConfigs(): array
    {
        $defaultPrefix = '/users';
        return [
            'prefix' => 'api'.$defaultPrefix,
            'middleware' => []
        ];
    }
}
