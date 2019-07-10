<?php

return [
    'feeds' => [
        'addon' => [
            'items' => 'App\Models\Article@getAllFeedItems',
            'url' => '/feed',
            'title' => 'All Addon Articles',
            'view' => 'feed::feed',
        ],
    ],
];
