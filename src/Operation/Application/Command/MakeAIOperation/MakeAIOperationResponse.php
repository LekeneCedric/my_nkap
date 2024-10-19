<?php

namespace App\Operation\Application\Command\MakeAIOperation;

class MakeAIOperationResponse
{
    public bool $operationOk = false;
    public string $consumedToken = '';
    public array $operations = [];
    public string $message = '';
}
