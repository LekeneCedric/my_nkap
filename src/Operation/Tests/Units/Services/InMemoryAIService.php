<?php

namespace App\Operation\Tests\Units\Services;

use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\VO\MakeAIOperationServiceResponseVO;

class InMemoryAIService implements AIService
{

    public function makeOperation(array $accounts, array $categories, string $message, string $currentDate, string $language): MakeAIOperationServiceResponseVO
    {
        return new MakeAIOperationServiceResponseVO(
            operations: [],
            operationIsOk: true,
            consumedToken: 100
        );
    }
}
