<?php

namespace App\FinancialGoal\Infrastructure\Provider;

use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\FinancialGoal\Domain\Subscriber\FinancialGoalEventSubscriber;
use App\FinancialGoal\Infrastructure\Repository\PdoFinancialGoalRepository;
use App\FinancialGoal\Infrastructure\Services\PdoCheckIfAccountExitByIdService;
use App\FinancialGoal\Infrastructure\Services\PdoCheckIfUserExistByIdService;
use App\Shared\Domain\Event\DomainEventPublisher;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FinancialGoalServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $domainEventPublisher = app(DomainEventPublisher::class);

        $domainEventPublisher->subscribe(
          new FinancialGoalEventSubscriber(
              financialGoalRepository: app(FinancialGoalRepository::class)
          ),
        );
    }

    public function register(): void
    {
        $this->loadMigrations();
        $this->registerRoutes();
        $this->bindModulesRepositories();
        $this->bindModulesServices();
    }
    private function bindModulesRepositories(): void
    {
        $this->app->singleton(FinancialGoalRepository::class, PdoFinancialGoalRepository::class);
    }

    private function bindModulesServices(): void
    {
        $this->app->singleton(CheckIfAccountExitByIdService::class, PdoCheckIfAccountExitByIdService::class);
        $this->app->singleton(CheckIfUserExistByIdService::class, PdoCheckIfUserExistByIdService::class);
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(base_path('src/FinancialGoal/Infrastructure/database/migrations'));
    }

    private function registerRoutes(): void
    {
        Route::group($this->routeConfigs(), function() {
           $this->loadRoutesFrom(
               base_path('src/FinancialGoal/Infrastructure/routes/web.php')
           );
        });
    }

    private function routeConfigs(): array
    {
        return [
            'prefix' => 'api/financial-goals',
            'middleware' => ['auth:sanctum']
        ];
    }

}
