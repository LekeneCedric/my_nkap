<?php

namespace App\User\Domain;

use App\Shared\VO\DateVO;
use App\Shared\VO\Id;
use App\Shared\VO\StringVO;
use Exception;

class User
{
    private ?DateVO $createdAt = null;
    public function __construct(
        private StringVO $name,
        private StringVO $email,
        private StringVO $password,
        private Id $userId,
        private Id $professionId,
    )
    {
    }

    public static function create(
        StringVO $name,
        StringVO $email,
        StringVO $password,
        Id $professionId,
        ?Id $userId = null
    ): User
    {
       $user = new User(
            name: $name,
            email: $email,
            password: $password,
            userId: $userId ?: new Id(),
            professionId: $professionId,
        );
       if (!$userId) {
           $user->createdAt = new DateVO();
       }
       return $user;
    }

    public function id(): Id
    {
        return $this->userId;
    }

    public function professionId(): Id
    {
        return $this->professionId;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->userId->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'password' => $this->password->value(),
            'created_at' => $this->createdAt->formatYMDHIS(),
        ];
    }
}
