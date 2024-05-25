<?php

namespace App\category\Tests\Units;

use App\category\Domain\UserCategory;
use App\category\Domain\UserCategoryRepository;
use App\Shared\VO\Id;

class InMemoryUserCategoryRepository implements UserCategoryRepository
{
    /**
     * @var UserCategory[]
     */
    public array $users = [];

    public function ofId(Id $id): ?UserCategory
    {
        return $this->users[$id->value()] ?? null;
    }

    public function save(?UserCategory $user): void
    {
        $this->users[$user->id->value()] = $user;
    }
}
