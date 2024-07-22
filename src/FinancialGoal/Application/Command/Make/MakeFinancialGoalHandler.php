<?php

namespace App\FinancialGoal\Application\Command\Make;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\FinancialGoal\Domain\Exceptions\ErrorOnSaveFinancialGoalException;
use App\FinancialGoal\Domain\FinancialGoal;
use App\FinancialGoal\Domain\FinancialGoalRepository;
use App\FinancialGoal\Domain\Service\CheckIfAccountExitByIdService;
use App\FinancialGoal\Domain\Service\CheckIfUserExistByIdService;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Domain\Exceptions\NotFoundUserException;

class MakeFinancialGoalHandler
{
    public function __construct(
        private readonly FinancialGoalRepository       $repository,
        private readonly CheckIfAccountExitByIdService $checkIfAccountExistByIdService,
        private readonly CheckIfUserExistByIdService $checkIfUserExistByIdService,
    )
    {
    }

    /**
     * @param MakeFinancialGoalCommand $command
     * @return MakeFinancialGoalResponse
     * @throws ErrorOnSaveFinancialGoalException
     * @throws NotFoundAccountException
     * @throws NotFoundUserException
     */
    public function handle(MakeFinancialGoalCommand $command): MakeFinancialGoalResponse
    {
        $this->checkIfAccountExistOrThrowNotFoundAccountException($command->accountId);
        $this->checkIfUserAlreadyExistOrThrowNotFoundException($command->userId);

        $financialGoalId = $command->financialGoalId ? new Id($command->financialGoalId) : null;
        $financialGoal = FinancialGoal::create(
            userId: new Id($command->userId),
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

    /**
     * @param string $userId
     * @return void
     * @throws NotFoundUserException
     */
    private function checkIfUserAlreadyExistOrThrowNotFoundException(string $userId): void
    {
        $userAlreadyExist = $this->checkIfUserExistByIdService->execute(new Id($userId));
        if (!$userAlreadyExist) {
            throw new NotFoundUserException('L\'utilisateur sélectionné n\'existe pas !');
        }
    }
}
