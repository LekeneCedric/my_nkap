@component('mail::message')
    # Verification Code

    Hello,

    You have requested to verify your account. Please use the following code to complete the verification process:

    ## **{{ $verification_code }}**

    This code will expire in **{{ $expiration_time }}** minutes.

    If you did not request this, please ignore this message.

    Thank you,
    The {{ $app_name }} Team
@endcomponent
