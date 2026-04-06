<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'broadcasting/auth', // ضروري تزيد هادي
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    // بدل '*' بالرابط ديال الـ Live Server ديالك (اللي هو http://127.0.0.1:5500)
    'allowed_origins' => ['http://127.0.0.1:5500', 'http://localhost:5500'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // رجع هادي true ضروري للـ Private Channels
    'supports_credentials' => true,

];
