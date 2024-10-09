<?php

namespace App\Bootstrap\Infrastructure\Providers;

use App\Account\Infrastructure\Provider\AccountServiceProvider;
use App\category\Infrastructure\Provider\CategoryServiceProvider;
use App\FinancialGoal\Infrastructure\Provider\FinancialGoalServiceProvider;
use App\Operation\Infrastructure\provider\OperationServiceProvider;
use App\Profession\Infrastructure\Provider\ProfessionServiceProvider;
use App\Shared\Domain\Event\DomainEventPublisher;
use App\Shared\Domain\Transaction\TransactionSession;
use App\Shared\Infrastructure\Logs\Provider\LogServiceProvider;
use App\Shared\Infrastructure\Transaction\EloquentTransactionSession;
use App\Statistics\Infrastructure\Providers\StatisticsServiceProvider;
use App\User\Infrastructure\Provider\UserProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->loadModuleServiceProviders();
        $this->loadDefaultMigrations();
        $this->bindModuleRepositories();
        $this->loadDomainEventSubscribers();
    }

    /**
     * @return void
     */
    private function loadModuleServiceProviders(): void
    {
        $this->app->register(AccountServiceProvider::class);
        $this->app->register(OperationServiceProvider::class);
        $this->app->register(UserProvider::class);
        $this->app->register(FinancialGoalServiceProvider::class);
        $this->app->register(ProfessionServiceProvider::class);
        $this->app->register(LogServiceProvider::class);
        $this->app->register(CategoryServiceProvider::class);
        $this->app->register(StatisticsServiceProvider::class);
    }

    /**
     * @return void
     */
    private function loadDefaultMigrations(): void
    {
        $this->loadMigrationsFrom(base_path('/src/Bootstrap/Infrastructure/database/migrations'));
    }

    /**
     * @return void
     */
    private function bindModuleRepositories(): void
    {
        $this->app->singleton(TransactionSession::class, EloquentTransactionSession::class);
    }

    private function loadDomainEventSubscribers(): void
    {
        $this->app->singleton(DomainEventPublisher::class, function() {
            return DomainEventPublisher::instance();
        });
    }
}
