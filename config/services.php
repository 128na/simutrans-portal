<?php

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
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI'),
        'access_restriction' => [
            'needs_phone_verification' => env('TWITTER_ACCESS_NEEDS_PHONE_VERIFICATION', true),
            'suspended' => env('TWITTER_ACCESS_SUSPENDED', true),
            'default_profile_image' => env('TWITTER_ACCESS_DEFAULT_PROFILE_IMAGE', false),
            'default_profile' => env('TWITTER_ACCESS_DEFAULT_PROFILE', false),
            'statuses_count' => env('TWITTER_ACCESS_STATUSES_COUNT', 0),
            'followers_count' => env('TWITTER_ACCESS_FOLLOWERS_COUNT', 0),
            'created_at' => env('TWITTER_ACCESS_CREATED_AT', 0),
            'email_suffix' => env('TWITTER_ACCESS_EMAIL_SUFFIX', ''),
        ],
        'register_restriction' => env('TWITTER_REGISTER_RESTRICTION'),
        'login_restriction' => env('TWITTER_LOGIN_RESTRICTION'),
    ],
];
