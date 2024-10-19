<?php

namespace App\Operation\Domain\Services;

use App\Operation\Domain\VO\MakeAIOperationServiceResponseVO;

interface AIService
{
    /**
     * @param array $categories
     * @param string $message
     * @param string $currentDate
     * @param string $language
     * @return MakeAIOperationServiceResponseVO
     */
    public function makeOperation(array $categories, string $message, string $currentDate, string $language): MakeAIOperationServiceResponseVO;
}
