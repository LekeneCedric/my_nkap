<?php

namespace App\FinancialGoal\Application\Command\Update;

use App\Operation\Domain\OperationTypeEnum;

class UpdateFinancialGoalCommand
{
    public function __construct(
        public string             $accountId,
        public float              $previousAmount,
        public float              $newAmount,
        public string             $operationDate,
        public ?OperationTypeEnum $type = null,
        public bool               $isDelete = false,
    )
    {
    }
}
