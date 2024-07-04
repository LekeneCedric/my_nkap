<?php

namespace App\category\Domain;

use App\category\Domain\Exceptions\ErrorOnSaveCategoryException;
use App\Shared\VO\Id;

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
