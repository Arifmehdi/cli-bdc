<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'admin/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000', 'http://localhost','http://localhost:8080'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

// return [

//     /*
//     |--------------------------------------------------------------------------
//     | Cross-Origin Resource Sharing (CORS) Configuration
//     |--------------------------------------------------------------------------
//     |
//     | Here you may configure your settings for cross-origin resource sharing
//     | or "CORS". This determines what cross-origin operations may execute
//     | in web browsers. You are free to adjust these settings as needed.
//     |
//     | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
//     |
//     */

//     // 'paths' => ['api/*', 'sanctum/csrf-cookie'],

//     // 'allowed_methods' => ['*'],

//     // 'allowed_origins' => ['*'],

//     // 'allowed_origins_patterns' => [],

//     // 'allowed_headers' => ['*'],

//     // 'exposed_headers' => [],

//     // 'max_age' => 0,

//     // 'supports_credentials' => true,

//     'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

//     'allowed_methods' => ['*'],

//     'allowed_origins' => ['http://localhost:3000'], // Be specific for security

//     'allowed_origins_patterns' => [],

//     'allowed_headers' => ['*'],

//     'exposed_headers' => [],

//     'max_age' => 0,

//     'supports_credentials' => true, // Crucial for Sanctum

// ];
