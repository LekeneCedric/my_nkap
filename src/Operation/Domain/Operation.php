<?php

namespace App\Operation\Domain;

use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;

class Operation
{
    private ?DateVO $createdAt = null;
    public function __construct(
        private Id                $operationId,
        private AmountVO          $amount,
        private OperationTypeEnum $type,
        private StringVO          $category,
        private StringVO          $detail,
        private DateVO            $date,
        private bool              $isDeleted,
    )
    {
    }

    public static function create(
        AmountVO          $amount,
        OperationTypeEnum $type,
        StringVO          $category,
        StringVO          $detail,
        DateVO            $date,
        ?Id               $id = null,
    )
    {
        $operation = new self(
            operationId: $id ?? new Id(),
            amount: $amount,
            type: $type,
            category: $category,
            detail: $detail,
            date: $date,
            isDeleted: false,
        );
        if (!$id) {
            $operation->createdAt = new DateVO();
        }
        return $operation;
    }

    public function id(): Id
    {
        return $this->operationId;
    }

    public function delete(): void
    {
        $this->isDeleted = true;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function type(): OperationTypeEnum
    {
        return $this->type;
    }

    public function amount(): AmountVO
    {
        return $this->amount;
    }

    /**
     * @throws Exception
     */
    public function toArray()
    {
        return [
            'uuid' => $this->operationId->value(),
            'type' => $this->type->value,
            'amount' => $this->amount->value(),
            'category' => $this->category->value(),
            'details' => $this->detail->value(),
            'date' => $this->date->formatYMDHIS(),
            'is_deleted' => $this->isDeleted ? 1 : 0,
            'created_at' => $this->createdAt->formatYMDHIS(),
        ];
    }
}
