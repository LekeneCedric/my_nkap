<?php

namespace App\category\Domain;

use App\Shared\Domain\VO\Id;

interface UserCategoryRepository
{
    /**
     * @param Id $id
     * @return UserCategory|null
     */
    public function ofId(Id $id): ?UserCategory;

    /**
     * @param UserCategory $user
     * @return void
     */
    public function save(UserCategory $user): void;
}
