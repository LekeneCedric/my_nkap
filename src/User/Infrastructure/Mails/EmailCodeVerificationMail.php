<?php

namespace App\User\Infrastructure\Mails;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class EmailCodeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verificationCode;
    public string $expirationTime;
    public string $appName;
    public function __construct($verificationCode, $expirationTime)
    {
        $this->verificationCode = $verificationCode;
        $this->expirationTime = $expirationTime;
        $this->appName = config('app.name');
    }

    public function build(): EmailCodeVerificationMail
    {
        return $this
            ->markdown('USER_VIEWS::emails.email_code_verification')
            ->subject('Your Account Verification Code')
            ->with([
                'verification_code' => $this->verificationCode,
                'expiration_time' => $this->expirationTime,
                'app_name' => $this->appName
            ]);
    }
}
