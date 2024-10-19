<?php

namespace App\Operation\Infrastructure\Services;

use App\Operation\Domain\OperationUser;
use App\Operation\Domain\Services\GetOperationUserService;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\User\Infrastructure\Models\User;

class EloquentGetOperationUserService implements GetOperationUserService
{

    public function execute(string $userId): ?OperationUser
    {
        $user = User::select(['uuid', 'token', 'token_updated_at'])->where('uuid', $userId)->first();
        if ($user === null) {
            return null;
        }
        return OperationUser::create(
            id: new Id($user->uuid),
            token: $user->token,
            updatedTokenDate: new DateVO($user->token_updated_at)
        );
    }
}
