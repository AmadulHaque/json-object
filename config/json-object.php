<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JSON Object Generation Path
    |--------------------------------------------------------------------------
    |
    | The directory where generated JSON objects will be placed.
    |
    */
    'path' => app_path('Json'),

    /*
    |--------------------------------------------------------------------------
    | JSON Object Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for generated JSON objects.
    |
    */
    'namespace' => 'App\\Json',

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features globally.
    |
    */
    'features' => [
        'validation' => true,
        'dirty_tracking' => true,
        'logging' => false,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Channel
    |--------------------------------------------------------------------------
    |
    | The logging channel to use when logging is enabled.
    |
    */
    'log_channel' => env('LOG_CHANNEL', 'stack'),
];
