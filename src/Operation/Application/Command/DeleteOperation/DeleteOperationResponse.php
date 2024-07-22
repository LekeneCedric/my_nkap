<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Operation\Domain\OperationTypeEnum;

class DeleteOperationResponse
{

    /**
     * @param string $message
     * @param bool $isDeleted
     * @param float $operationAmount
     * @param string $date
     * @param OperationTypeEnum $operationType
     */
    public function __construct(
        public string $message,
        public bool $isDeleted,
        public float $operationAmount,
        public string $date,
        public OperationTypeEnum $operationType,
    )
    {
    }
}
