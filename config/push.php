<?php

$path = env('FIREBASE_CREDENTIALS');

return [
    'notifier' => env('PUSH_NOTIFIER', 'none'),
    'notifiers' => [
        'none' => [], // does nothing with notification
        'log' => [], // writes to log file notification info
        'firebase' => [
            'account' => $path ? dirname(__DIR__) . '/' . ltrim($path, '/') : null,
        ],
    ],
];
