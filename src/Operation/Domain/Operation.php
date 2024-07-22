<?php

namespace App\Operation\Domain;

use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use Exception;

class Operation
{
    private ?DateVO $createdAt = null;
    private ?DateVO $updatedAt = null;
    private ?DateVO $deletedAt = null;
    private OperationEventStateEnum $eventState;

    public function __construct(
        private Id                $operationId,
        private AmountVO          $amount,
        private OperationTypeEnum $type,
        private Id          $categoryId,
        private StringVO          $detail,
        private DateVO            $date,
        private bool              $isDeleted,
    )
    {
    }

    public static function create(
        AmountVO          $amount,
        OperationTypeEnum $type,
        Id          $categoryId,
        StringVO          $detail,
        DateVO            $date,
        ?Id               $id = null,
    ): Operation
    {
        $operation = new self(
            operationId: $id ?? new Id(),
            amount: $amount,
            type: $type,
            categoryId: $categoryId,
            detail: $detail,
            date: $date,
            isDeleted: false,
        );
        if (!$id) {
            $operation->eventState = OperationEventStateEnum::onCreate;
            $operation->createdAt = new DateVO();
        }
        return $operation;
    }

    public function update(
        AmountVO          $amount,
        OperationTypeEnum $type,
        Id          $categoryId,
        StringVO          $detail,
        DateVO            $date
    ): void
    {
        $this->eventState = OperationEventStateEnum::onUpdate;
        $this->updatedAt = new DateVO();
        $this->amount = $amount;
        $this->type = $type;
        $this->categoryId = $categoryId;
        $this->detail = $detail;
        $this->date = $date;
    }

    public function id(): Id
    {
        return $this->operationId;
    }

    public function delete(): void
    {
        $this->eventState = OperationEventStateEnum::onDelete;
        $this->deletedAt = new DateVO();
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

    public function categoryId(): Id
    {
        return $this->categoryId;
    }

    /**
     * @throws Exception
     */
    public function toArray(): array
    {
        $data = [
            'uuid' => $this->operationId->value(),
            'type' => $this->type->value,
            'amount' => $this->amount->value(),
            'details' => $this->detail->value(),
            'date' => $this->date->formatYMDHIS(),
            'is_deleted' => $this->isDeleted ? 1 : 0,
        ];
        if ($this->eventState === OperationEventStateEnum::onCreate) {
            $data['created_at'] = $this->createdAt->formatYMDHIS();
        }
        if ($this->eventState === OperationEventStateEnum::onUpdate) {
            $data['updated_at'] = $this->updatedAt->formatYMDHIS();
        }
        return $data;
    }

    public function deletedAt(): ?DateVO
    {
        return $this->deletedAt;
    }

    /**
     * @return OperationEventStateEnum
     */
    public function eventState(): OperationEventStateEnum
    {
        return $this->eventState;
    }

    public function date(): DateVO
    {
        return $this->date;
    }
}
