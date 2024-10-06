<?php

namespace App\User\Domain\Traits;

trait GenerateVerificationCodeTrait
{
    public function generateVerificationCode(int $length): string
    {
        $verificationCode = '';
        $characters = '0123456789';
        $charactersLengths = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomPosition = rand(0, $charactersLengths - 1);
            $verificationCode .= $characters[$randomPosition];
        }
        return $verificationCode;
    }
}
