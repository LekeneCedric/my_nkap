<?php

namespace App\FinancialGoal\Domain\Subscriber;

use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalCommand;
use App\FinancialGoal\Application\Command\Update\UpdateFinancialGoalHandler;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Operation\Domain\Events\OperationDeleted;
use App\Operation\Domain\Events\OperationSaved;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventSubscriber;
use Exception;

class FinancialGoalEventSubscriber implements DomainEventSubscriber
{
    public function __construct(
        private FinancialGoalRepository $financialGoalRepository,
    )
    {
    }

    /**
     * @param DomainEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(DomainEvent $event): void
    {
        if (get_class($event) === OperationSaved::class) {
            $command = new UpdateFinancialGoalCommand(
                accountId: $event->accountId,
                previousAmount: $event->previousAmount,
                newAmount: $event->newAmount,
                operationDate: $event->operationDate,
                type: $event->type
            );
            (new UpdateFinancialGoalHandler(
                repository: $this->financialGoalRepository
            ))->handle($command);
        }
        if (get_class($event) === OperationDeleted::class) {
            $command = new UpdateFinancialGoalCommand(
                accountId: $event->accountId,
                previousAmount: $event->previousAmount,
                newAmount: $event->newAmount,
                operationDate: $event->date,
                type: $event->type,
                isDelete: $event->isDeleted
            );
            (new UpdateFinancialGoalHandler(
                repository: $this->financialGoalRepository
            ))->handle($command);
        }
    }

    public function isSubscribeTo(DomainEvent $event): bool
    {
        return get_class($event) === OperationSaved::class ||
            get_class($event) === OperationDeleted::class;
    }
}
