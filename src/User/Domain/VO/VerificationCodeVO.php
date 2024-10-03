<?php

namespace App\User\Domain\VO;

use App\User\Domain\Traits\GenerateVerificationCodeTrait;

class VerificationCodeVO
{
    use GenerateVerificationCodeTrait;
    const VERIFICATION_CODE_LENGTH = 6;
    const EXPIRATION_TIME_MINUTES = 10;
    private string $currentVerificationCode;
    private int $expirationTime;
    public function __construct()
    {
        $this->currentVerificationCode = $this->generateVerificationCode(self::VERIFICATION_CODE_LENGTH);
        $this->expirationTime = strtotime("+".self::EXPIRATION_TIME_MINUTES." minutes");
    }

    public function verificationCode(): string
    {
        return $this->currentVerificationCode;
    }

    public function expirationTime(): int
    {
        return $this->expirationTime;
    }

}
