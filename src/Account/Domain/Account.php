<?php

namespace App\Account\Domain;

use App\Account\Domain\Enums\AccountEventStateEnum;
use App\Shared\VO\AmountVO;
use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;

class Account
{
    private ?DateVO $createdAt = null;
    private ?DateVO $updatedAt = null;
    private ?DateVO $deletedAt = null;
    private ?AccountEventStateEnum $eventState;

    public function __construct(
        private Id $userId,
        private Id $accountId,
        private StringVO $name,
        private StringVo $type,
        private StringVO $icon,
        private StringVO $color,
        private AmountVO $balance,
        private bool $isIncludeInTotalBalance,
        private bool $isDeleted,
    )
    {
    }

    public static function create(
        Id $userId,
        StringVO $name,
        StringVO $type,
        StringVO $icon,
        StringVO $color,
        AmountVO $balance,
        bool $isIncludeInTotalBalance,
        ?bool $isDeleted = false,
        ?Id $accountId = null,
    )
    {
        $account = new self(
            userId: $userId,
            accountId: $accountId ?? new Id(),
            name: $name,
            type: $type,
            icon: $icon,
            color: $color,
            balance: $balance,
            isIncludeInTotalBalance: $isIncludeInTotalBalance,
            isDeleted: $isDeleted,
        );
        if (!$accountId) {
            $account->eventState = AccountEventStateEnum::onCreate;
            $account->createdAt = new DateVO();
        }
        if ($accountId) {
            $account->eventState = AccountEventStateEnum::onUpdate;
            $account->updatedAt = new DateVO();
        }

        return $account;
    }

    public function id(): Id
    {
        return $this->accountId;
    }

    public function name(): StringVO
    {
        return $this->name;
    }

    public function update(
        StringVO $name,
        StringVO $type,
        StringVO $icon,
        StringVO $color,
        AmountVO $balance,
        bool $isIncludeInTotalBalance
    ): void
    {
        $this->eventState = AccountEventStateEnum::onUpdate;
        $this->updatedAt = new DateVO();
        $this->name = $name;
        $this->type = $type;
        $this->icon = $icon;
        $this->color = $color;
        $this->balance = $balance;
        $this->isIncludeInTotalBalance  = $isIncludeInTotalBalance;
    }

    public function delete(): void
    {
        $this->eventState = AccountEventStateEnum::onDelete;
        $this->updatedAt = new DateVO();
        $this->deletedAt = new DateVO();
        $this->isDeleted = true;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function eventState(): ?AccountEventStateEnum
    {
        return $this->eventState;
    }

    public function deletedAt(): ?DateVO
    {
        return $this->deletedAt;
    }

    public function userId(): Id
    {
        return $this->userId;
    }

    /**
     * @throws \Exception
     */
    public function toArray(): array
    {
        $data = [
          'uuid' => $this->id()->value(),
          'name' => $this->name->value(),
          'type' => $this->type->value(),
          'icon' => $this->icon->value(),
          'color' => $this->color->value(),
          'balance' => $this->balance->value(),
          'is_include_in_total_balance' => $this->isIncludeInTotalBalance ? 1 : 0,
          'is_deleted' => $this->isDeleted ? 1 : 0
        ];
        if ($this->eventState === AccountEventStateEnum::onUpdate) {
            $data['updated_at'] = $this->updatedAt->formatYMDHIS();
        }
        if ($this->eventState === AccountEventStateEnum::onCreate) {
            $data['created_at'] = $this->createdAt->formatYMDHIS();
            $data['total_incomes'] = 0;
            $data['total_expenses'] = 0;
        }
        return $data;
    }
}
