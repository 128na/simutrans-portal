<?php

declare(strict_types=1);

use App\Repositories\Article\FeedRepository;

return [
    'feeds' => [
        'addon' => [
            'url' => '/feed',
            'title' => '全てのアドオン',
            'items' => [FeedRepository::class, 'addon'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128' => [
            'url' => '/feed/pak128',
            'title' => 'Pak128',
            'description' => '更新日順',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '128'],
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128-japan' => [
            'url' => '/feed/pak128-japan',
            'title' => 'Pak128.Japan',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '128-japan'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak64' => [
            'url' => '/feed/pak64',
            'title' => 'Pak64',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '64'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'page' => [
            'url' => '/feed/page',
            'title' => '一般記事',
            'items' => [FeedRepository::class, 'page'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'announce' => [
            'url' => '/feed/announce',
            'title' => 'お知らせ記事',
            'items' => [FeedRepository::class, 'announce'],
            'description' => '更新日順',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
    ],
];
