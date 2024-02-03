<?php

namespace App\Operation\Application\Command\DeleteOperation;

class DeleteOperationResponse
{

    /**
     * @param bool $isDeleted
     */
    public function __construct(
        public string $message,
        public bool $isDeleted
    )
    {
    }
}
