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
        'token' => env('DISCORD_TOKEN', 'dummy-token'),
        'channel' => env('DISCORD_CHANNEL', 'dummy-channel'),
        'domain' => 'https://discord.gg',
        'max_age' => 300,
        'max_uses' => 1,
    ],
    'google_recaptcha' => [
        'credential' => env('GOOGLE_RECAPTCHA_CREDENTIAL', 'dummy-credential.json'),
        'projectName' => env('GOOGLE_RECAPTCHA_PROJECT_NAME', 'dummy-project-name'),
        'siteKey' => env('GOOGLE_RECAPTCHA_SITE_KEY', 'dummy-site-key'),
    ],
    'twitter' => [
        // STANDALONE APPS > Settings > User authentication settings
        'client_id' => env('TWITTER_CLIENT_ID', 'dummy-client-id'),
        'client_secret' => env('TWITTER_CLIENT_SECRET', 'dummy-client-secret'),
        // STANDALONE APPS > Keys and tokens > Authentication Tokens
        'bearer_token' => env('TWITTER_BEARER_TOKEN', 'dummy-bearer-token'),
        // STANDALONE APPS > Keys and tokens > Consumer Keys
        'consumer_key' => env('TWITTER_CONSUMER_KEY', 'dummy-consumer-key'),
        'consumer_secret' => env('TWITTER_CONSUMER_SECRET', 'dummy-consumer-secret'),
    ],
    'misskey' => [
        'base_url' => 'https://misskey.io/api',
        'token' => env('MISSKEY_TOKEN', 'dummy-token'),
    ],
    'bluesky' => [
        'user' => env('BLUESKY_USER', 'dummy-user'),
        'password' => env('BLUESKY_PASSWORD', 'dummy-password'),
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
        ],
    ],

];
