<?php

namespace App\FinancialGoal\Domain;

use App\FinancialGoal\Domain\Dto\FinancialGoalDto;
use App\FinancialGoal\Domain\Enum\FinancialGoalEventStateEnum;
use App\Shared\Domain\VO\AmountVO;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
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
        private Id       $userId,
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
        Id        $userId,
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
            userId: $userId,
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

    public function userId(): Id
    {
        return $this->userId;
    }

    /**
     * @return Id
     */
    public function accountId(): Id
    {
        return $this->accountId;
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

    public function toDto(): FinancialGoalDto
    {
        return new FinancialGoalDto(
          id: $this->financialGoalId->value(),
          accountId: $this->accountId->value(),
          currentAmount: $this->currentAmount->value(),
          isComplete: $this->isComplete,
        );
    }

    public function retrieveAmount(float $previousAmount): void
    {
        $updatedAmount = $this->currentAmount->value() - $previousAmount;
        $this->currentAmount = new AmountVO($updatedAmount);
        $this->updateIsCompleteStatus();
    }

    public function addAmount(float $amount): void
    {
        $updatedAmount = $this->currentAmount->value() + $amount;
        $this->currentAmount = new AmountVO($updatedAmount);
        $this->updateIsCompleteStatus();
    }
    private function updateIsCompleteStatus(): void
    {
        if ($this->currentAmount >= $this->desiredAmount) {
            $this->isComplete = true;
        }
        if ($this->currentAmount < $this->desiredAmount) {
            $this->isComplete = false;
        }
    }

    /**
     * @return DateVO
     */
    public function startDate(): DateVO
    {
        return $this->startDate;
    }

    /**
     * @return DateVO
     */
    public function endDate(): DateVO
    {
        return $this->endDate;
    }
}
