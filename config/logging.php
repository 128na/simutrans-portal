<?php

declare(strict_types=1);

$prod = env('APP_ENV') === 'production';

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => 'general',

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'slowquery' => [
            'driver' => 'daily',
            'path' => storage_path('logs/slowquery.log'),
            'level' => 'info',
        ],
        // stack定義
        // 一般ログ
        'general' => [
            'driver' => 'stack',
            'channels' => $prod ? ['file_daily', 'discord_error'] : ['file_daily'],
            'ignore_exceptions' => false,
        ],

        // ユーザー起点のアクション通知
        'audit' => [
            'driver' => 'stack',
            'channels' => $prod ? ['file_audit', 'discord_notification'] : ['file_audit'],
            'ignore_exceptions' => false,
        ],

        'invite' => [
            'driver' => 'stack',
            'channels' => $prod ? ['file_invite', 'discord_invite'] : ['file_invite'],
            'ignore_exceptions' => false,
        ],

        'worker' => [
            'driver' => 'daily',
            'path' => storage_path('logs/worker.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],

        // 各種ログ設定
        'discord_error' => [
            'driver' => 'custom',
            'via' => MarvinLabs\DiscordLogger\Logger::class,
            'level' => 'error',
            'url' => env('DISCORD_WEBHOOK_ERROR'),
        ],

        'discord_notification' => [
            'driver' => 'custom',
            'via' => MarvinLabs\DiscordLogger\Logger::class,
            'level' => 'info',
            'url' => env('DISCORD_WEBHOOK_NOTIFICATION'),
        ],

        'discord_invite' => [
            'driver' => 'custom',
            'via' => MarvinLabs\DiscordLogger\Logger::class,
            'level' => 'info',
            'url' => env('DISCORD_WEBHOOK_INVITE'),
        ],

        'file_daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 365,
        ],

        'file_single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'file_audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit.log'),
            'level' => 'debug',
            'days' => 365,
        ],
        'file_invite' => [
            'driver' => 'daily',
            'path' => storage_path('logs/discord-invite.log'),
            'level' => 'debug',
            'days' => 365,
        ],
    ],
];
