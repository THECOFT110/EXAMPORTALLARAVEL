<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'name' => env('APP_NAME', 'SALU Exam Portal'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Karachi'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'version' => env('APP_VERSION', '1.0.0'),

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Security & Business Configuration Constants
    |--------------------------------------------------------------------------
    */
    'seats_per_room' => (int) env('SEATS_PER_ROOM', 30),
    'photo_max_width' => (int) env('PHOTO_MAX_WIDTH', 400),
    'photo_max_height' => (int) env('PHOTO_MAX_HEIGHT', 500),
    'photo_jpeg_quality' => (int) env('PHOTO_JPEG_QUALITY', 80),
    'photo_max_size_kb' => (int) env('PHOTO_MAX_SIZE_KB', 2048),
    'doc_max_size_kb' => (int) env('MAX_FILE_SIZE', 5120),
    'enrollment_fee_amount' => (float) env('ENROLLMENT_FEE_AMOUNT', 1500.00),
    'exam_fee_amount' => (float) env('EXAM_FEE_AMOUNT', 2000.00),
    'late_fee_amount' => (float) env('LATE_FEE_AMOUNT', 500.00),
    'challan_validity_days' => (int) env('CHALLAN_VALIDITY_DAYS', 7),
];
