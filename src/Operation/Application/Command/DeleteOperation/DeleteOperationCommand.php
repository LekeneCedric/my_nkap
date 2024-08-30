<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Command\Command;

class DeleteOperationCommand extends Command
{
    public ?float $previousAmount = null;
    public ?float $newAmount = null;
    public ?string $date = null;
    public ?OperationTypeEnum $type = null;
    public ?bool $isDeleted = null;
    public ?string $monthlyStatisticsComposedId = null;
    public ?string $monthlyStatisticsByCategoryComposedId = null;
    public ?string $userId = null;
    public ?int $year = null;
    public ?int $month = null;
    public ?string $categoryId = null;
    public function __construct(
        public string $accountId,
        public string $operationId,
    )
    {
    }
}
