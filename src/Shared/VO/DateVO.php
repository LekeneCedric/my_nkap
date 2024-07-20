<?php

namespace App\Shared\VO;

use App\FinancialGoal\Domain\Enum\ComparisonEnum;
use DateTime;
use Exception;

class DateVO
{
    private string $value;
    private string $format;
    public function __construct(?string $value = null, ?string $format = null)
    {
        if ($value) {
            $this->value = $value;
        } else {
            $this->value = date('Y-m-d H:i:s');
        }
        $this->format = $format ?? 'Y-m-d H:i:s';
        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function formatYMDHIS(): string
    {
        if (!$this->value) {
            throw new Exception(' La date n\'est pas valide !');
        }
            return (new DateTime($this->value))->format('Y-m-d H:i:s');
    }

    private function validate(): void
    {
        $d = DateTime::createFromFormat($this->format, $this->value);
        if (!$d || $d->format($this->format) != $this->value) {
            throw new \InvalidArgumentException("La date entrée n'est pas valide");
        }
    }

    /**
     * @throws Exception
     */
    public function compare(DateVO $startDate): int
    {
        $date1 = new DateTime($this->value);
        $date2 = new DateTime($startDate->formatYMDHIS());
        if ($date1 < $date2) {
            return ComparisonEnum::GREATER->value;
        }
        if ($date1 > $date2) {
            return ComparisonEnum::LESS->value;
        }
        return ComparisonEnum::EQUAL->value;
    }
}
