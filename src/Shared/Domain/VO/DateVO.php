<?php

namespace App\Shared\Domain\VO;

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

    /**
     * @return string
     * @throws Exception
     */
    public function formatYMD(): string
    {
        if (!$this->value) {
            throw new Exception(' La date n\'est pas valide !');
        }
        return (new DateTime($this->value))->format('Y-m-d');
    }

    private function validate(): void
    {
        $d = DateTime::createFromFormat($this->format, $this->value);
        if (!$d || $d->format($this->format) != $this->value) {
            throw new \InvalidArgumentException("La date entrée n'est pas valide !");
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
            return ComparisonEnum::LESS->value;
        }
        if ($date1 > $date2) {
            return ComparisonEnum::GREATER->value;
        }
        return ComparisonEnum::EQUAL->value;
    }

    public function year(): int
    {
        $timestamp = strtotime($this->value);
        return date('Y', $timestamp);
    }

    public function month(): int
    {
        $timestamp = strtotime($this->value);
        return date('m', $timestamp);
    }
}
