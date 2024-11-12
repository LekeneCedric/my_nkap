<?php

namespace App\Operation\Application\Command\MakeOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Shared\Domain\Command\Command;
use App\Shared\Domain\Command\CommandHandler;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\Subscription\Domain\Exceptions\SubscriptionCannotPermitOperationException;
use App\Subscription\Domain\Services\SubscriptionService;
use App\User\Domain\Repository\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class MakeOperationHandler implements CommandHandler
{
    use StatisticsComposedIdBuilderTrait;

    public function __construct(
        private OperationAccountRepository $repository,
        private UserRepository             $userRepository,
        private ChannelNotification        $channelNotification,
        private SubscriptionService        $subscriptionService,
    )
    {
    }

    /**
     * @param MakeOperationCommand|Command $command
     * @return makeOperationResponse
     */
    public function handle(MakeOperationCommand|Command $command): makeOperationResponse
    {
        $response = new makeOperationResponse();
        DB::beginTransaction();
        try {
            $command->userId = $this->userRepository->userId();
            if (!$command->operationId) {
                $this->subscriptionService->checkIfCanMakeOperation(userId: $command->userId);
            }
            $operationAccount = $this->getOperationAccountOrThrowNotFoundException($command->accountId);
            if ($command->operationId) {
                $response->previousOperationAmount = $operationAccount->operation($command->operationId)->amount()->value();
                $operationAccount->updateOperation(
                    operationId: new Id($command->operationId),
                    amount: new AmountVO($command->amount),
                    previousAmount: new AmountVO($command->previousAmount ?? 0),
                    type: $command->type,
                    categoryId: new Id($command->categoryId),
                    detail: new StringVO($command->detail),
                    date: new DateVO($command->date),
                );
            }
            if (!$command->operationId) {
                $operationAccount->makeOperation(
                    amount: new AmountVO($command->amount),
                    type: $command->type,
                    categoryId: new Id($command->categoryId),
                    detail: new StringVO($command->detail),
                    date: new DateVO($command->date)
                );
            }

            $this->repository->saveOperation($operationAccount);
            $this->completCommandWithAdditionalInformation(
                $command
            );
            $operationAccount->publishOperationSaved($command);
            if (!$command->operationId) {
                $this->subscriptionService->retrieveOperation(userId: $command->userId, count: 1);
            }
            DB::commit();
            $response->operationSaved = true;
            $response->operationId = $operationAccount->currentOperation()->id()->value();
        } catch (
            NotFoundAccountException|
            SubscriptionCannotPermitOperationException $e) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $response->message = $e->getMessage();
            $this->channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'OPERATION (CREATE)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::WARNING->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        } catch (Exception $e) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $response->message = ErrorMessagesEnum::TECHNICAL;
            $this->channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'OPERATION (CREATE)',
                        'message' => $e->getMessage(),
                        'level' => ErrorLevelEnum::CRITICAL->value,
                        'command' => json_encode($command, JSON_PRETTY_PRINT),
                        'trace' => "Error in file: $file on line: $line"
                    ],
                )
            );
        }

        return $response;
    }

    /**
     * @param string $accountId
     * @return operationAccount|null
     * @throws NotFoundAccountException
     */
    private function getOperationAccountOrThrowNotFoundException(string $accountId): ?operationAccount
    {
        $account = $this->repository->byId(new Id($accountId));
        if (!$account) {
            throw new NotFoundAccountException();
        }
        return $account;
    }

    private function completCommandWithAdditionalInformation(MakeOperationCommand|Command &$command): void
    {
        list($year, $month) = [(new DateVO($command->date))->year(), (new DateVO($command->date))->month()];
        $command->year = $year;
        $command->month = $month;
        $command->monthlyStatsComposedId = $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $command->userId);
        $command->monthlyStatsByCategoryComposedId = $this->buildMonthlyCategoryStatisticsComposedId(month: $month, year: $year, userId: $command->userId, categoryId: $command->categoryId);
    }
}
