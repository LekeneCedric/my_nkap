<?php

namespace App\Shared\VO;

class AmountVO
{
    public function __construct(
        private float $value
    )
    {
//        $this->validate($this->value);
    }

    private function validate(float $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("Montant invalide ! un montant ne peux être négatif !");
        }
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
}
