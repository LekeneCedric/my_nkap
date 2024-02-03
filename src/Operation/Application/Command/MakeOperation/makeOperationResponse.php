<?php

namespace App\Operation\Application\Command\MakeOperation;

class makeOperationResponse
{
    public function __construct(
        public bool   $operationSaved = false,
        public string $operationId = '',
    )
    {
    }


}
