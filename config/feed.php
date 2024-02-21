<?php

declare(strict_types=1);

use App\Repositories\Article\FeedRepository;

return [
    'feeds' => [
        'addon' => [
            'url' => '/feed',
            'title' => '新着アドオン',
            'items' => [FeedRepository::class, 'addon'],
            'description' => '新着アドオン（更新日順）',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128' => [
            'url' => '/feed/pak128',
            'title' => 'Pak128の新着アドオン',
            'description' => '新着アドオン（更新日順）',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '128'],
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak128-japan' => [
            'url' => '/feed/pak128-japan',
            'title' => 'Pak128Japanの新着アドオン',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '128-japan'],
            'description' => '新着アドオン（更新日順）',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'addon.pak64' => [
            'url' => '/feed/pak64',
            'title' => 'Pak64の新着アドオン',
            'items' => [FeedRepository::class, 'pakAddon', 'pakSlug' => '64'],
            'description' => '新着アドオン（更新日順）',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'page' => [
            'url' => '/feed/page',
            'title' => '一般記事の新着',
            'items' => [FeedRepository::class, 'page'],
            'description' => '新着記事（更新日順）',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
        'announce' => [
            'url' => '/feed/announce',
            'title' => 'お知らせ記事の新着',
            'items' => [FeedRepository::class, 'announce'],
            'description' => '新着記事（更新日順）',
            'language' => 'ja-JP',
            'format' => 'atom',
            'view' => 'feed::atom',
        ],
    ],
];
