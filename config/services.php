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

    'sms' => [
        'driver' => env('SMS_DRIVER', 'null'),
    ],

    'crm' => [
        'api_key' => env('CRM_API_KEY'),
        'base_uri' => env('CRM_BASE_URI'),
    ],

    'dynamics_crm' => [
        'base_uri' => env('DYNAMICS_CRM_BASE_URI'),
    ],

    'property_feed' => [
        'base_uri' => env('PROPERTY_FEED_BASE_URI'),
    ],

    'mortgage' => [
        'base_uri' => env('MORTGAGE_API_BASE_URL'),
        'email' => env('MORTGAGE_MANAGER_EMAIL'),
        'password' => env('MORTGAGE_MANAGER_PASSWORD')
    ],

    'feedback' => [
        'base_uri' => env('FEEDBACK_URI'),
        'emails' => env('FEEDBACK_EMAILS')?explode(',', env('FEEDBACK_EMAILS')):null,
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN', '5837259414:AAGZT2DbJozB9lbaKdmJfqeJyBnwNPKN258')
    ],
];
