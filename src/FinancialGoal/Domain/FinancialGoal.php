<?php

namespace App\FinancialGoal\Domain;

use App\FinancialGoal\Domain\Enum\FinancialGoalEventStateEnum;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;

class FinancialGoal
{
    private ?bool $isDeleted = false;
    private ?DateVO $createdAt = null;
    private ?DateVO $updatedAt = null;
    private ?DateVO $deletedAt = null;
    private ?FinancialGoalEventStateEnum $eventState = null;

    public function __construct(
        private Id       $financialGoalId,
        private Id       $accountId,
        private DateVO   $startDate,
        private DateVO   $endDate,
        private AmountVO $currentAmount,
        private AmountVO $desiredAmount,
        private StringVO $details,
        public bool      $isComplete
    )
    {
    }

    public static function create(
        Id        $accountId,
        DateVO    $startDate,
        DateVO    $enDate,
        AmountVO  $desiredAmount,
        StringVO  $details,
        ?Id       $financialGoalId = null,
        ?AmountVO $currentAmount = null,
        ?bool     $isComplete = false,
    ): FinancialGoal
    {
        $self = new self(
            financialGoalId: $financialGoalId ?? new Id(),
            accountId: $accountId,
            startDate: $startDate,
            endDate: $enDate,
            currentAmount: $currentAmount ?? new AmountVO(0),
            desiredAmount: $desiredAmount,
            details: $details,
            isComplete: $isComplete,
        );
        if (!$financialGoalId) {
            $self->eventState = FinancialGoalEventStateEnum::onCreate;
            $self->createdAt = new DateVO();
        }
        if ($financialGoalId) {
            $self->eventState = FinancialGoalEventStateEnum::onUpdate;
            $self->updatedAt = new DateVO();
        }
        return $self;
    }

    public function delete(): void
    {
        $this->eventState = FinancialGoalEventStateEnum::onDelete;
        $this->isDeleted = true;
        $this->deletedAt = new DateVO();
    }

    /**
     * @return Id
     */
    public function id(): Id
    {
        return $this->financialGoalId;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function eventState(): ?FinancialGoalEventStateEnum
    {
        return $this->eventState;
    }

    public function createdAt(): ?DateVO
    {
        return $this->createdAt;
    }

    /**
     * @return DateVO|null
     */
    public function deletedAt(): ?DateVO
    {
        return $this->deletedAt;
    }

    public function updatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return AmountVO
     */
    public function desiredAmount(): AmountVO
    {
        return $this->desiredAmount;
    }

    public function changeDesiredAmount(AmountVO $desiredAmount): void
    {
        $this->eventState = FinancialGoalEventStateEnum::onUpdate;
        $this->updatedAt = new DateVO();
        $this->desiredAmount = $desiredAmount;
    }

    public function changeDetails(StringVO $details): void
    {
        $this->eventState = FinancialGoalEventStateEnum::onUpdate;
        $this->updatedAt = new DateVO();
        $this->details = $details;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $data = [
            'uuid' => $this->financialGoalId->value(),
            'end_date' => $this->endDate->formatYMDHIS(),
            'details' => $this->details->value(),
            'desired_amount' => $this->desiredAmount->value(),
        ];
        if ($this->eventState === FinancialGoalEventStateEnum::onCreate) {
            $data['account_id'] = $this->accountId->value();
            $data['start_date'] = $this->startDate->formatYMDHIS();
            $data['current_amount'] = $this->currentAmount->value();
            $data['created_at'] = $this->createdAt->formatYMDHIS();
        }
        if ($this->eventState === FinancialGoalEventStateEnum::onUpdate) {
            $data['updated_at'] = $this->updatedAt->formatYMDHIS();
        }

        return $data;
    }

    /**
     * @return Id
     */
    public function accountId(): Id
    {
        return $this->accountId;
    }
}
