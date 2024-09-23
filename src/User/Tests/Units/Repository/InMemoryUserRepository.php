<?php

namespace App\User\Tests\Units\Repository;

use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;

class InMemoryUserRepository implements UserRepository
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function userId(): string
    {
        return 'userId';
    }
}
