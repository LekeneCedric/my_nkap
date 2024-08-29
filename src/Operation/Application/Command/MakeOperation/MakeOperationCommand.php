<?php

namespace App\Operation\Application\Command\MakeOperation;

use App\Operation\Domain\OperationTypeEnum;
use App\Shared\Domain\Command\Command;

class MakeOperationCommand extends Command
{
    public ?string $operationId = null;
    public ?float $previousAmount = null;
    public ?string $userId = null;
    public ?string $monthlyStatsComposedId = null;
    public ?string $monthlyStatsByCategoryComposedId = null;
    public ?int $year = null;
    public ?int $month = null;
    public function __construct(
        public string            $accountId,
        public OperationTypeEnum $type,
        public float             $amount,
        public string            $categoryId,
        public string            $detail,
        public string            $date
    )
    {
    }
}
