<?php

namespace App\FinancialGoal\Application\Command\Delete;

use App\FinancialGoal\Domain\Exception\NotFoundFinancialGoalException;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\Shared\VO\Id;

class DeleteFinancialGoalHandler
{
    public function __construct(
        private FinancialGoalRepository $repository,
    )
    {
    }

    /**
     * @throws NotFoundFinancialGoalException
     */
    public function handle(DeleteFinancialGoalCommand $command): DeleteFinancialGoalResponse
    {
        $financialGoal = $this->getFinancialGoalOrThrowNotFoundFinancialGoalException($command->financialGoalId);

        $financialGoal->delete();

        $this->repository->save($financialGoal);

        return new DeleteFinancialGoalResponse(
            status: true,
            isDeleted: true,
        );
    }

    /**
     * @param string $financialGoalId
     * @return FinancialGoal
     * @throws NotFoundFinancialGoalException
     */
    private function getFinancialGoalOrThrowNotFoundFinancialGoalException(string $financialGoalId): FinancialGoal
    {
        $financialGoal = $this->repository->byId(new Id($financialGoalId));
        if (!$financialGoal) {
            throw new NotFoundFinancialGoalException("L'objectif financier sélectionner n'existe plus");
        }
        return $financialGoal;
    }
}
