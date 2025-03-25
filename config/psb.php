<?php

$certsPath = env('PSB_MERCHANTS_PATH');
$freeCertsPath = env('PSB_FREE_MERCHANTS_PATH');

return [
    'host' => env('PSB_HOST'),
    'base_uri' => env('PSB_BASE_URI'),
    'certs_path' => $certsPath ? dirname(__DIR__) . '/' . ltrim($certsPath, '/') : null,
    'free_certs_path' => $freeCertsPath ? dirname(__DIR__) . '/' . ltrim($freeCertsPath, '/') : null,
    'terminal' => env('PSB_TERMINAL'),
    'merchant' => env('PSB_MERCHANT'),
    'key' => env('PSB_KEY'),
    'second_key' => env('PSB_SECOND_KEY'),
];
