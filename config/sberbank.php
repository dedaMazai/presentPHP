<?php

$filePath = env('SBERBANK_CREDENTIALS_PATH');
$certsPath = env('SBERBANK_CERTS_PATH');

return [
    'base_uri' => env('SBERBANK_BASE_URI'),
    'credentials' => $filePath ? dirname(__DIR__) . '/' . ltrim($filePath, '/') : null,
    'certs_path' => $certsPath ? dirname(__DIR__) . '/' . ltrim($certsPath, '/') : null,
    'sales_seller_id' => env('SBERBANK_SALES_SELLER_ID'),
    'sber_booking' => [
        'booking_uri' => env('SBER_BOOKING_URI'),
        'booking_username' => env('SBER_BOOKING_USERNAME'),
        'booking_password' => env('SBER_BOOKING_PASSWORD'),
        'booking_inn' => env('SBER_BOOKING_INN')
    ]
];
