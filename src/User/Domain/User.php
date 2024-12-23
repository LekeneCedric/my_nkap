<?php

namespace App\User\Domain;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\VO\DateVO;
use App\Shared\Domain\VO\Id;
use App\Shared\Domain\VO\StringVO;
use App\User\Domain\Enums\UserStatusEnum;
use App\User\Domain\Exceptions\UnknownVerificationCodeException;
use App\User\Domain\Exceptions\VerificationCodeNotMatchException;
use App\User\Domain\VO\VerificationCodeVO;
use Exception;

class User extends AggregateRoot
{
    private ?DateVO $createdAt = null;

    public function __construct(
        private StringVO            $name,
        private StringVO            $email,
        private StringVO            $password,
        private Id                  $userId,
        private Id                  $professionId,
        private UserStatusEnum      $status,
        private ?VerificationCodeVO $verificationCode = null,
    )
    {
        parent::__construct();
    }

    public static function create(
        StringVO $name,
        StringVO $email,
        StringVO $password,
        Id       $professionId,
        ?Id      $userId = null
    ): User
    {
        $user = new User(
            name: $name,
            email: $email,
            password: $password,
            userId: $userId ?: new Id(),
            professionId: $professionId,
            status: UserStatusEnum::PENDING,
            verificationCode: new VerificationCodeVO(),
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

    public function assignVerificationCode(): void
    {
        $this->verificationCode = new VerificationCodeVO();
    }

    public function verificationCode(): string
    {
        return $this->verificationCode->verificationCode();
    }

    public function email(): StringVO
    {
        return $this->email;
    }

    /**
     * @param string $code
     * @return void
     * @throws UnknownVerificationCodeException
     * @throws VerificationCodeNotMatchException
     */
    public function checkIfCodeIsCorrectOrThrowException(string $code): void
    {
        if (!$this->verificationCode) {
            throw new UnknownVerificationCodeException();
        }
        if (!$this->verificationCode->isValid($code)) {
            throw new VerificationCodeNotMatchException();
        }
    }

    public function resetPassword(string $password): void
    {
        $this->password = new StringVO(bcrypt($password));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $data = [
            'uuid' => $this->userId->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
            'password' => $this->password->value(),
            'status' => $this->status->value,
        ];
        if ($this->createdAt) {
            $data['created_at'] = $this->createdAt->formatYMDHIS();
        }
        if ($this->verificationCode) {
            $data['verification_code'] = $this->verificationCode->verificationCode();
            $data['verification_code_exp'] = $this->verificationCode->expirationTime();
        }
        return $data;
    }

    public function publicInfo(): array
    {
        return [
          'name' => $this->name->value(),
          'email' => $this->email->value(),
        ];
    }
    public function activateAccount(): void
    {
        $this->status = UserStatusEnum::ACTIVE;
    }

    public function publishUserVerified(): void
    {
        $this->domainEventPublisher->publish(
            new UserVerified(
                userId: $this->userId->value(),
            )
        );
    }
}
