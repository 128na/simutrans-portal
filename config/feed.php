<?php

return [
    'feeds' => [
        'addon' => [
            'items' => 'App\Services\FeedService@getAllFeedItems',
            'url' => '/feed',
            'title' => 'All Addon Articles',
            'view' => 'feed::feed',
        ],
    ],
];
