<?php

namespace App\category\Domain;

use App\category\Domain\Enums\CategoryEventStateEnum;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;

class Category
{
    private ?CategoryEventStateEnum $eventState;
    public function __construct(
        public readonly Id       $categoryId,
        public readonly StringVO $icon,
        public readonly StringVO $name,
        public readonly StringVO $color,
        public readonly StringVO $description,
    )
    {
        $this->eventState = null;
    }

    public static function create(
        StringVO $icon,
        StringVO $name,
        StringVO $color,
        StringVO $description,
        Id $id = null,
    ): Category
    {
        $isCreation = is_null($id);
        $self = new self(
            categoryId: $id ?? new Id(),
            icon: $icon,
            name: $name,
            color: $color,
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

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->categoryId->value(),
            'icon' => $this->icon->value(),
            'name' => $this->name->value(),
            'color' => $this->color->value(),
            'description' => $this->description->value(),
        ];
    }

    public function changeEventState(CategoryEventStateEnum $eventState): void
    {
        $this->eventState = $eventState;
    }
}
