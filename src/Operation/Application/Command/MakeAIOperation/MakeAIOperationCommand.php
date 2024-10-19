<?php

namespace App\Operation\Application\Command\MakeAIOperation;

class MakeAIOperationCommand
{
    public function __construct(
        public string $userId,
        public array $categories,
        public string $currentDate,
        public string $message,
        public string $language,
    )
    {
    }
}
