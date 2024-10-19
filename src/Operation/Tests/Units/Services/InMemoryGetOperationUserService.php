<?php

namespace App\Operation\Tests\Units\Services;

use App\Operation\Domain\OperationUser;
use App\Operation\Domain\Services\GetOperationUserService;

class InMemoryGetOperationUserService implements GetOperationUserService
{
    /**
     * @var OperationUser[]
     */
    public array $users = [];

    /**
     * @param string $userId
     * @return OperationUser|null
     */
    public function execute(string $userId): ?OperationUser
    {
        if (!array_key_exists($userId, $this->users)) {
            return null;
        }
        return $this->users[$userId];
    }
}
