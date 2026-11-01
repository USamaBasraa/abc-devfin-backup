<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'), // 997033107214-o745c68p36kms1e1uc3gl7a3t692ra0k.apps.googleusercontent.com
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // beby6PMkaH5dovPAoFd50-c8
        'redirect' => env('APP_URL').'/google/auth/callback', // 'http://localhost/laravel-8/public/google-auth/callback',
    ],

    'pinnacle' => [
        'base_url' => env('PINNACLE_API_BASE_URL'),
        'token' => env('PINNACLE_API_TOKEN'),
        'username' => env('PINNACLE_API_USERNAME'),
        'password' => env('PINNACLE_API_PASSWORD'),
        'external_tokens_url' => env('PINNACLE_API_EXTERNAL_TOKENS_URL'),
        'token_from_date' => env('PINNACLE_API_TOKEN_FROM_DATE'),
        'token_no_of_days' => env('PINNACLE_API_TOKEN_NO_OF_DAYS', 7),
        'token_no_of_persons' => env('PINNACLE_API_TOKEN_NO_OF_PERSONS', 1),
    ],

];
