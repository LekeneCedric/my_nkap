<?php

namespace App\Account\Tests\Units\Builders;

use App\Account\Application\Command\Save\SaveAccountCommand;

class SaveAccountCommandBuilder
{
    private ?string $accountId = null;
    private ?string $name = null;
    private ?string $type = null;
    private ?string $icon = null;
    private ?string $color = null;
    private ?float $balance = null;
    private ?bool $isIncludeInTotalBalance = null;

    public static function asCommand(): SaveAccountCommandBuilder
    {
        return new self();
    }

    public function withName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function withType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function withIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function withColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function withBalance(float $balance): static
    {
        $this->balance = $balance;
        return $this;
    }

    public function withIsIncludeInTotalBalance(bool $isIncludeInTotalBalance): static
    {
        $this->isIncludeInTotalBalance = $isIncludeInTotalBalance;
        return $this;
    }

    public function withAccountId(string $accountId): static
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function build(): SaveAccountCommand
    {
        $command =  new SaveAccountCommand(
            name: $this->name,
            type: $this->type,
            icon: $this->icon,
            color: $this->color,
            balance: $this->balance,
            isIncludeInTotalBalance: $this->isIncludeInTotalBalance,
        );
        $command->accountId = $this->accountId;
        return $command;
    }
}
