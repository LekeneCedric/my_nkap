<?php

namespace App\category\Domain;

use App\category\Domain\Enums\EventState\CategoryEventStateEnum;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class Category
{
    private ?CategoryEventStateEnum $eventState;
    public function __construct(
        public readonly Id       $categoryId,
        public readonly StringVO $icon,
        public readonly StringVO $name,
        public readonly StringVO $description,
    )
    {
        $this->eventState = null;
    }

    public static function create(
        StringVO $icon,
        StringVO $name,
        StringVO $description,
        Id $id = null,
    ): Category
    {
        $isCreation = is_null($id);
        $self = new self(
            categoryId: $id ?? new Id(),
            icon: $icon,
            name: $name,
            description: $description
        );
        $self->eventState = $isCreation
            ? CategoryEventStateEnum::onCreate
            : CategoryEventStateEnum::onUpdate;
        return $self;
    }

    /**
     * @return CategoryEventStateEnum|null
     */
    public function eventState(): ?CategoryEventStateEnum
    {
        return $this->eventState;
    }
}
