<?php

namespace App\Operation\Domain;

use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\User\Domain\Enums\UserTokenEnum;

class OperationUser
{
    private function __construct(
        private Id     $id,
        private int    $aiToken,
        private DateVO $tokenUpdatedAt,
    )
    {
    }

    /**
     * @param Id $id
     * @param int $aiToken
     * @param DateVO $updatedTokenDate
     * @return OperationUser
     */
    public static function create(
        Id     $id,
        int    $aiToken,
        DateVO $updatedTokenDate,
    ): OperationUser
    {
        if ($updatedTokenDate->isFromPreviousDay()) {
            $aiToken = UserTokenEnum::DEFAULT_TOKEN;
        }
        return new self(
            id: $id,
            aiToken: $aiToken,
            tokenUpdatedAt: $updatedTokenDate,
        );
    }
    public function retrievedConsumedToken(mixed $consumedToken): void
    {
        $this->aiToken -= $consumedToken;
        $this->tokenUpdatedAt = new DateVO();
        if ($this->aiToken < 0) {
            $this->aiToken = 0;
        }
    }

    /**
     * @return int
     */
    public function token(): int
    {
        return $this->aiToken;
    }

    /**
     * @return Id
     */
    public function id(): Id
    {
        return $this->id;
    }

    public function tokenUpdatedAt(): DateVO
    {
        return $this->tokenUpdatedAt;
    }
}
