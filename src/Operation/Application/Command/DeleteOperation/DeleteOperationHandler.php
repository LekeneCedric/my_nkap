<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Account\Domain\Exceptions\NotFoundAccountException;
use App\Operation\Domain\Enums\OperationsMessagesEnum;
use App\Operation\Domain\Exceptions\NotFoundOperationException;
use App\Operation\Domain\operationAccount;
use App\Operation\Domain\OperationAccountRepository;
use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Command\Command;
use App\Shared\Domain\Command\CommandHandler;
use App\Shared\Domain\Notifications\Channel\ChannelNotification;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationContent;
use App\Shared\Domain\Notifications\Channel\ChannelNotificationTypeEnum;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Infrastructure\Enums\ErrorLevelEnum;
use App\Shared\Infrastructure\Enums\ErrorMessagesEnum;
use App\Statistics\Infrastructure\Trait\StatisticsComposedIdBuilderTrait;
use App\User\Domain\Repository\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteOperationHandler implements CommandHandler
{
    use StatisticsComposedIdBuilderTrait;

    public function __construct(
        private readonly OperationAccountRepository $repository,
        private readonly UserRepository             $userRepository,
        private readonly ChannelNotification        $channelNotification,
    )
    {
    }

    /**
     * @param DeleteOperationCommand|Command $command
     * @return DeleteOperationResponse
     */
    public function handle(DeleteOperationCommand|Command $command): DeleteOperationResponse
    {
        $response = new DeleteOperationResponse();
        DB::beginTransaction();
        try {
            $operationAccount = $this->getOperationAccountOrThrowException($command);
            $operationAccount->deleteOperation(new Id($command->operationId));

            $this->repository->saveOperation($operationAccount);

            $this->completeCommandWithAdditionalInformations(
                $command,
                $operationAccount->currentOperation()->amount()->value(),
                $operationAccount->currentOperation()->date()->formatYMDHIS(),
                $operationAccount->currentOperation()->type(),
                $operationAccount->currentOperation()->categoryId()->value(),
            );
            $operationAccount->publishOperationDeleted($command);
            DB::commit();
            $response->message = OperationsMessagesEnum::DELETED;
            $response->isDeleted = true;
        } catch (
        NotFoundAccountException|
        NotFoundOperationException $e
        ) {
            DB::rollBack();
            $file = $e->getFile();
            $line = $e->getLine();
            $response->message = $e->getMessage();
            $this->channelNotification->send(
                new ChannelNotificationContent(
                    type: ChannelNotificationTypeEnum::ISSUE,
                    data: [
                        'module' => 'OPERATION (DELETE)',
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
                        'module' => 'OPERATION (DELETE)',
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
     * @param DeleteOperationCommand $command
     * @return operationAccount
     * @throws NotFoundAccountException
     */
    private function getOperationAccountOrThrowException(DeleteOperationCommand $command): operationAccount
    {
        $accountId = $command->accountId;
        $account = $this->repository->byId(new Id($accountId));
        if (!$account) {
            throw new NotFoundAccountException();
        }
        return $account;
    }

    private function completeCommandWithAdditionalInformations(
        DeleteOperationCommand|Command $command,
        float                          $amount,
        string                         $operationDate,
        OperationTypeEnum              $type,
        string                         $categoryId
    ): void
    {
        list($year, $month) = [(new DateVO($operationDate))->year(), (new DateVO($operationDate))->month()];

        $userId = $this->userRepository->userId();
        $command->userId = $userId;
        $command->year = $year;
        $command->month = $month;
        $command->categoryId = $categoryId;
        $command->previousAmount = $amount;
        $command->newAmount = $amount;
        $command->date = $operationDate;
        $command->type = $type;
        $command->isDeleted = true;
        $command->monthlyStatisticsComposedId = $this->buildMonthlyStatisticsComposedId(month: $month, year: $year, userId: $userId);
        $command->monthlyStatisticsByCategoryComposedId = $this->buildMonthlyCategoryStatisticsComposedId(month: $month, year: $year, userId: $userId, categoryId: $command->categoryId);
    }
}
