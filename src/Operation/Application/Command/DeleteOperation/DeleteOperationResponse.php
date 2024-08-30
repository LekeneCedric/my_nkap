<?php

namespace App\Operation\Application\Command\DeleteOperation;

use App\Operation\Domain\OperationTypeEnum;

class DeleteOperationResponse
{

    /**
     * @param string $message
     * @param bool $isDeleted
     */
    public function __construct(
        public string $message = '',
        public bool $isDeleted = false,
    )
    {
    }
}
