<?php

namespace App\User\Domain\Enums;

enum UserMessagesEnum: string
{
    case NOT_FOUND = 'not_found_user';
    case RECOVER_PASSWORD_CODE_SENT = 'verification_code_sent';
}
