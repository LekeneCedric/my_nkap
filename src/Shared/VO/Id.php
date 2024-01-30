<?php

namespace App\Shared\VO;

use Ramsey\Uuid\Uuid;

class Id
{
    public function __construct(
        private ?string $value = null
    )
    {
        if (empty($this->value)) {
            $this->value = self::generateId();
        }
    }
    private static function generateId(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function value(): ?string
    {
        return $this->value;
    }

}
