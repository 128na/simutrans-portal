<?php

declare(strict_types=1);

return [
    'consumer_key' => env('TWITTER_CONSUMER_KEY', ''),
    'consumer_secret' => env('TWITTER_CONSUMER_SECRET', ''),
    'access_token' => env('TWITTER_ACCESS_TOKEN', ''),
    'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET', ''),
    'bearer_token' => env('TWITTER_BEARER_TOKEN', ''),
    'client_id' => env('TWITTER_CLIENT_ID', ''),
    'client_secret' => env('TWITTER_CLIENT_SECRET', ''),

    // https://twitter.com/i/lists/1533065708212396033
    'list_id' => '1533065708212396033',

    // https://developer.twitter.com/en/docs/twitter-api/users/lookup/api-reference/get-users-me
    'user_id' => '718733002918875137',
];
