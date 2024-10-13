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

    public $tries = 5; // The number of times the job should be retried

    public $timeout = 120; // The maximum number of seconds the job can run

    protected $email;
    protected $code;

    public function __construct(
        string $email,
        string $code
    ){
        $this->email = $email;
        $this->code = $code;
    }

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new EmailCodeVerificationMail($this->code, '10 minutes'));
    }
}
