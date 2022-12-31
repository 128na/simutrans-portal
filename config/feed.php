<?php

declare(strict_types=1);

return [
    'feeds' => [
        'addon' => [
            'items' => [\App\Repositories\ArticleRepository::class, 'findAllFeedItems'],
            'url' => '/feed',
            'title' => 'All Addon Articles',
            'description' => '新着記事',
            'language' => 'ja-JP',
            'view' => 'feed::feed',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
    ],
];
