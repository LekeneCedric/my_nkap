<?php

namespace App\User\Infrastructure\Jobs;

use App\User\Infrastructure\Mails\EmailCodeVerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVerificationCodeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $code
    ){}

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new EmailCodeVerificationMail($this->code, '10 minutes'));
    }
}
