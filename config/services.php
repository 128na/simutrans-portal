<?php

use App\Services\Social\GoogleLoginService;
use App\Services\Social\TwitterLoginService;

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
        'service_class' => TwitterLoginService::class,

        'access_restriction' => [
            'needs_phone_verification' => env('TWITTER_ACCESS_RESTRICTION_NEEDS_PHONE_VERIFICATION', true),
            'suspended' => env('TWITTER_ACCESS_RESTRICTION_SUSPENDED', true),
            'default_profile_image' => env('TWITTER_ACCESS_RESTRICTION_DEFAULT_PROFILE_IMAGE', true),
            'default_profile' => env('TWITTER_ACCESS_RESTRICTION_DEFAULT_PROFILE', true),
            'statuses_count' => env('TWITTER_ACCESS_RESTRICTION_STATUSES_COUNT', 10000),
            'followers_count' => env('TWITTER_ACCESS_RESTRICTION_FOLLOWERS_COUNT', 10000),
            'created_at' => env('TWITTER_ACCESS_RESTRICTION_CREATED_AT', 10000),
            'email_suffix' => env('TWITTER_ACCESS_RESTRICTION_EMAIL_SUFFIX', '@test.example'),
        ],
        'register_restriction' => env('TWITTER_REGISTER_RESTRICTION', true),
        'login_restriction' => env('TWITTER_LOGIN_RESTRICTION', true),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'service_class' => GoogleLoginService::class,

        'access_restriction' => [
            'email_verified' => env('GOOGLE_ACCESS_RESTRICTION_EMAIL_VERIFIED', true),
        ],
        'register_restriction' => env('GOOGLE_REGISTER_RESTRICTION', true),
        'login_restriction' => env('GOOGLE_LOGIN_RESTRICTION', true),
    ],
];
