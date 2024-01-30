<?php

namespace App\Shared\VO;

use InvalidArgumentException;

class StringVO
{
    public function __construct(
        private readonly string $value
    )
    {
        $this->validate($this->value);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException("chaine de caractère invalide !");
        }
    }
}
