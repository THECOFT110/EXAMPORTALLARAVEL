<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Google Cloud Vision, JazzCash, Resend, and more.
    |
    */

    'google_vision' => [
        'api_key' => env('GOOGLE_VISION_API_KEY'),
        'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS'),
        'endpoint' => env('GOOGLE_VISION_ENDPOINT', 'https://vision.googleapis.com/v1/images:annotate'),
    ],

    'jazzcash' => [
        'merchant_id' => env('JAZZCASH_MERCHANT_ID'),
        'password' => env('JAZZCASH_PASSWORD'),
        'salt' => env('JAZZCASH_SALT'),
        'return_url' => env('JAZZCASH_RETURN_URL'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

];
