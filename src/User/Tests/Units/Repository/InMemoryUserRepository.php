<?php

namespace App\User\Tests\Units\Repository;

use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\User;

class InMemoryUserRepository implements UserRepository
{

    public function create(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function userId(): string
    {
        return 'userId';
    }

    public function ofEmail(string $email): ?User
    {
        return null;
    }

    public function update(User $user): void
    {
        //
    }

    public function of(string $email, UserStatusEnum $status): ?User
    {
        //
    }
}
