<?php

namespace App\User\Application\Command\Login;

use App\Shared\Domain\VO\DateVO;
use App\Subscription\Domain\Services\SubscriptionService;
use App\User\Domain\Enums\UserTokenEnum;
use App\User\Infrastructure\Exceptions\NotFoundUserException;
use App\User\Infrastructure\Models\Profession;
use App\User\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginHandler
{

    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    )
    {
    }

    /**
     * @param LoginCommand $command
     * @return LoginResponse
     * @throws NotFoundUserException
     */
    public function handle(LoginCommand $command): LoginResponse
    {
        $response = new LoginResponse();

        $user = User::where('email', $command->email)->where('is_active', true)->where('is_deleted', false)->first();

        if (!$user || !Hash::check($command->password, $user->password)) {
            throw new NotFoundUserException();
        }

        $userData = [
          'userId' => $user->uuid,
          'email' => $user->email,
          'name' => $user->name,
          'profession' => Profession::where('id', $user->profession_id)->first()->name,
        ];
        $userSubscriptionData = $this->subscriptionService->getUserSubscriptionData($user->uuid);

        $leftNbToken = $userSubscriptionData['nb_token'];
        $leftNbOperations = $userSubscriptionData['nb_operations'];
        $leftNbAccounts = $userSubscriptionData['nb_accounts'];
        $leftTokenUpdatedAt = new DateVO(date("Y-m-d H:i:s", $userSubscriptionData['nb_token_updated_at']));
        $leftNbOperationsUpdatedAt = new DateVO(date("Y-m-d H:i:s", $userSubscriptionData['nb_operations_updated_at']));

        if ($leftTokenUpdatedAt->isFromPreviousDay()) {
            $leftNbToken = $userSubscriptionData['nb_token_per_day'];
            $this->subscriptionService->updateUserToken($user->uuid, $leftNbToken);
        }
        if ($leftNbOperationsUpdatedAt->isFromPreviousDay()) {
            $leftNbOperations = $userSubscriptionData['nb_operations_per_day'];
            $this->subscriptionService->updateUserNbOperations($user->uuid, $leftNbOperations);
        }
        $token = $user->createToken(env('TOKEN_KEY'))->plainTextToken;

        $response->isLogged = true;
        $response->user  = $userData;
        $response->token = $token;
        $response->leftNbToken = $leftNbToken;
        $response->leftNbOperations = $leftNbOperations;
        $response->leftNbAccounts = $leftNbAccounts;
        $response->subscriptionId = $userSubscriptionData['subscriptionId'];
        $response->subscriptionStartDate = $userSubscriptionData['start_date'];
        $response->subscriptionEndDate = $userSubscriptionData['end_date'];
        $response->nbTokenUpdatedAt = $userSubscriptionData['nb_token_updated_at'];
        $response->nbOperationsUpdatedAt = $userSubscriptionData['nb_operations_updated_at'];
        $response->nbTokenPerDay = $userSubscriptionData['nb_token_per_day'];
        $response->nbOperationsPerDay = $userSubscriptionData['nb_operations_per_day'];
        return $response;
    }
}
