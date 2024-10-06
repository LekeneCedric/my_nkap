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

    /**
     * @param string|null $verificationCode
     * @param int|null $expirationTime
     */
    public function __construct(?string $verificationCode=null, ?int $expirationTime=null)
    {
        $this->currentVerificationCode =  $verificationCode ?? $this->generateVerificationCode(self::VERIFICATION_CODE_LENGTH);
        $this->expirationTime = $expirationTime ?? strtotime("+".self::EXPIRATION_TIME_MINUTES." minutes");
    }

    /**
     * @return string
     */
    public function verificationCode(): string
    {
        return $this->currentVerificationCode;
    }

    /**
     * @return int
     */
    public function expirationTime(): int
    {
        return $this->expirationTime;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function isValid(string $code): bool
    {
        if ($this->currentVerificationCode !== $code) {
            return false;
        }
        if ($this->expirationTime < time()) {
            return false;
        }
        return true;
    }

}
