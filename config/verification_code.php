<?php

return [
    'daily_limit' => env('VERIFICATION_CODE_LIMIT_DAILY', 3),

    'generator' => env('VERIFICATION_CODE_GENERATOR', 'numerical'),

    'expiry' => 10 * 60, // seconds for expiry

    'generators' => [
        'numerical' => [
            'length' => 4,
        ],

        'fixed' => [
            'code' => env('VERIFICATION_CODE', '1234'),
        ],
    ],
];
