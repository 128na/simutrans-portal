<?php

return [
    'feeds' => [
        'addon' => [
            'items' => 'App\Repositories\ArticleRepository@findAllFeedItems',
            'url' => '/feed',
            'title' => 'All Addon Articles',
            'view' => 'feed::feed',
        ],
    ],
];
