<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the OTP settings for your application.
    | These settings control how OTP codes are generated and validated.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | OTP Time To Live (TTL)
    |--------------------------------------------------------------------------
    |
    | This value determines the number of minutes an OTP code will remain
    | valid before expiring. The default is 10 minutes.
    |
    */

    'ttl' => env('OTP_TTL', 10),

    /*
    |--------------------------------------------------------------------------
    | OTP Length
    |--------------------------------------------------------------------------
    |
    | This value determines the number of digits in the OTP code.
    | The default is 4 digits (1000-9999).
    |
    */

    'length' => env('OTP_LENGTH', 4),

];


