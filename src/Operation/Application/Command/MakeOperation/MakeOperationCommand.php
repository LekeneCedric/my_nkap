<?php

namespace App\Operation\Application\Command\MakeOperation;

use App\Operation\Domain\OperationTypeEnum;

class MakeOperationCommand
{
    public ?string $operationId = null;
    public function __construct(
        public string            $accountId,
        public OperationTypeEnum $type,
        public float             $amount,
        public string            $category,
        public string            $detail,
        public string            $date
    )
    {
    }
}
