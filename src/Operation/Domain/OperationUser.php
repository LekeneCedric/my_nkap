<?php

namespace App\Operation\Domain;

use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\User\Domain\Enums\UserTokenEnum;

class OperationUser
{
    private function __construct(
        private Id     $id,
        private int    $token,
        private DateVO $tokenUpdatedAt,
    )
    {
    }

    /**
     * @param Id $id
     * @param int $token
     * @param DateVO $updatedTokenDate
     * @return OperationUser
     */
    public static function create(
        Id $id,
        int $token,
        DateVO $updatedTokenDate,
    ): OperationUser
    {
        if ($updatedTokenDate->isFromPreviousDay()) {
            $token = UserTokenEnum::DEFAULT_TOKEN;
        }
        return new self(
            id: $id,
            token: $token,
            tokenUpdatedAt: $updatedTokenDate,
        );
    }
    public function retrievedConsumedToken(mixed $consumedToken): void
    {
        $this->token -= $consumedToken;
    }

    /**
     * @return int
     */
    public function token(): int
    {
        return $this->token;
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
