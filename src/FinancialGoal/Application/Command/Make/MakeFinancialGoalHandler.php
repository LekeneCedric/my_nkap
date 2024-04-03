<?php

namespace App\FinancialGoal\Application\Command\Make;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class MakeFinancialGoalHandler
{
    public function __construct(
        private readonly FinancialGoalRepository       $repository,
        private readonly CheckIfAccountExitByIdService $checkIfAccountExistByIdService,
    )
    {
    }

    /**
     * @param MakeFinancialGoalCommand $command
     * @return MakeFinancialGoalResponse
     * @throws NotFoundAccountException
     * @throws ErrorOnSaveFinancialGoalException
     */
    public function handle(MakeFinancialGoalCommand $command): MakeFinancialGoalResponse
    {
        $this->checkIfAccountExistOrThrowNotFoundAccountException($command->accountId);

        $financialGoalId = $command->financialGoalId ? new Id($command->financialGoalId) : null;
        $financialGoal = FinancialGoal::create(
            accountId: new Id($command->accountId),
            startDate: new DateVO($command->startDate),
            enDate: new DateVO($command->endDate),
            desiredAmount: new AmountVO($command->desiredAmount),
            details: new StringVO($command->details),
            financialGoalId: $financialGoalId
        );

        $this->repository->save($financialGoal);

        $response = new  MakeFinancialGoalResponse(
            status: true,
            isMake: true,
        );

        if (!$command->financialGoalId) {
            $response->message = "Objectif financié créer avec succès !";
            $response->financialGoalId = $financialGoal->id()->value();
            $response->createdAt = $financialGoal->createdAt()->formatYMDHIS();
        }
        if ($command->financialGoalId) {
            $response->message = "Objectif financié modifié avec succès !";
        }

        return $response;
    }

    /**
     * @param string $accountId
     * @return void
     * @throws NotFoundAccountException
     */
    private function checkIfAccountExistOrThrowNotFoundAccountException(string $accountId): void
    {
        $accountAlreadyExist = $this->checkIfAccountExistByIdService->execute(new Id($accountId));
        if (!$accountAlreadyExist) {
            throw new NotFoundAccountException('Le compte sélectionné n\'existe pas !');
        }
    }
}
