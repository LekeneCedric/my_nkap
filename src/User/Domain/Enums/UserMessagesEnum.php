<?php

namespace App\User\Domain\Enums;

enum UserMessagesEnum: string
{
    const NOT_FOUND = 'not_found_user';
    const RECOVER_PASSWORD_CODE_SENT = 'verification_code_sent';
    const UNKNOWN_VERIFICATION_CODE = 'unknown_verification_code';
    const VERIFICATION_CODE_NOT_MATCH = 'verification_code_not_match';
    const ACCOUNT_VERIFIED = 'account_verified';
    const LOGOUT = 'logout';
    const ALREADY_EXIST_WITH_SAME_EMAIL = 'user_already_exist_with_same_email';
    const REGISTERED = 'user_registered';
    const PASSWORD_RESET = 'password_reset_message';
}
