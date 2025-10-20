<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'discord' => [
        'token' => env('DISCORD_TOKEN'),
        'channel' => env('DISCORD_CHANNEL'),
        'domain' => 'https://discord.gg',
        'max_age' => 300,
        'max_uses' => 1,
    ],
    'google_recaptcha' => [
        'credential' => env('GOOGLE_RECAPTCHA_CREDENTIAL'),
        'projectName' => env('GOOGLE_RECAPTCHA_PROJECT_NAME'),
        'siteKey' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
    ],
    'twitter' => [
        // STANDALONE APPS > Settings > User authentication settings
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        // STANDALONE APPS > Keys and tokens > Authentication Tokens
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        // STANDALONE APPS > Keys and tokens > Consumer Keys
        'consumer_key' => env('TWITTER_CONSUMER_KEY'),
        'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
    ],
    'misskey' => [
        'base_url' => 'https://misskey.io/api',
        'token' => env('MISSKEY_TOKEN'),
    ],
    'bluesky' => [
        'user' => env('BLUESKY_USER'),
        'password' => env('BLUESKY_PASSWORD'),
    ],
    'markdown' => [
        'allowed_elements' => [
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'pre',
            'code',
            'blockquote',
            'table',
            'tr',
            'td',
            'th',
            'thead',
            'tbody',
            'strong',
            'em',
            'b',
            'i',
            'u',
            's',
            'span',
            'a',
            'p',
            'br',
            'ul',
            'ol',
            'li',
            'img',
        ]
    ]

];
