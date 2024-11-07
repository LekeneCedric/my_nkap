<?php

namespace App\Shared\Domain\VO;

use App\FinancialGoal\Domain\Enum\ComparisonEnum;
use DateTime;
use Exception;

class DateVO
{
    private string $value;
    private string $format;

    /**
     * @throws Exception
     */
    public function __construct(?string $value = null, ?string $format = null, ?string $extraTime = null)
    {
        if ($value) {
            $this->value = $value;
        } else {
            if (!$extraTime) {
                $this->value = date('Y-m-d H:i:s');
            } else {
                $date = new DateTime();
                $date->modify($extraTime);
                $this->value = $date->format('Y-m-d H:i:s');
            }
        }
        if ($format) {
            $this->format = $format;
        } else {
            // Check for different date formats
            if (preg_match('/^\d{4}-\d{2}-\d{2}(\s\d{1,2}(:\d{2})?(:\d{2})?)?$/', $this->value)) {
                // Match: Y-m-d, Y-m-d H, Y-m-d H:i, or Y-m-d H:i:s
                if (str_contains($this->value, ':')) {
                    // Check if seconds are present
                    $parts = explode(':', $this->value);
                    if (count($parts) == 2) {
                        $this->format = 'Y-m-d H:i'; // Y-m-d H:i format
                    } else {
                        $this->format = 'Y-m-d H:i:s'; // Y-m-d H:i:s format
                    }
                } else {
                    $this->format = 'Y-m-d'; // Y-m-d format
                }
            } else {
                $this->format = 'Y-m-d H:i:s'; // Default to full date-time format
            }
        }
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

    public function isFromPreviousDay()
    {
        // Get today's date in Y-m-d format (ignoring time)
        $today = (new DateTime())->format('Y-m-d');

        // Format the entered date to Y-m-d for comparison
        $formattedEnteredDate = (new DateTime($this->value))->format('Y-m-d');

        // Return true if the entered date is earlier than today
        return $formattedEnteredDate < $today;
    }

    /**
     * @return int
     */
    public function timestamp(): int
    {
        return strtotime($this->value);
    }
}
