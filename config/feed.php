<?php

declare(strict_types=1);

use App\Services\FeedService;

return [
    'feeds' => [
        'addon' => [
            'url' => '/feed',
            'title' => '全てのアドオン',
            'items' => [FeedService::class, 'pakAll'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128' => [
            'url' => '/feed/pak128',
            'title' => 'Pak128',
            'description' => '更新日順',
            'items' => [FeedService::class, 'latestPak', 'pak' => '128'],
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128-japan' => [
            'url' => '/feed/pak128-japan',
            'title' => 'Pak128.Japan',
            'items' => [FeedService::class, 'latestPak', 'pak' => '128-japan'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak64' => [
            'url' => '/feed/pak64',
            'title' => 'Pak64',
            'items' => [FeedService::class, 'latestPak', 'pak' => '64'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'page' => [
            'url' => '/feed/page',
            'title' => '記事',
            'items' => [FeedService::class, 'page'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'announce' => [
            'url' => '/feed/announce',
            'title' => 'お知らせ記事',
            'items' => [FeedService::class, 'announce'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
    ],
];
