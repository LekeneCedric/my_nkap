<?php

namespace App\Operation\Application\Command\MakeManyOperations;

class MakeManyOperationResponse
{
    public bool $operationsSaved = false;
    public array $operationIds = [];
    public string $message = '';
}
