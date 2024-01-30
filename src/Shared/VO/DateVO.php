<?php

namespace App\Shared\VO;

class DateVO
{
    private string $value;
    public function __construct(
    )
    {
        $this->value = date('Y-m-d H:i:s');
    }

    /**
     * @throws \Exception
     */
    public function formatYMDHIS(): string
    {
        if (!$this->value) {
            throw new \Exception(' La date n\'est pas valide !');
        }
        return (new \DateTime($this->value))->format('Y-m-d H:i:s');
    }
}
