<?php

declare(strict_types=1);

return [
    'article' => [
        'create' => "新規投稿「:title」\n:url\nby :name\nat :at\n:tags",
        'update' => "「:title」更新\n:url\nby :name\nat :at\n:tags",
    ],
    'simple_article' => [
        'create' => "新規投稿「:title」\nby :name",
        'update' => "「:title」更新\nby :name",
    ],
    'screenshot' => [
        'create' => "スクリーンショット投稿『:title』\n:url\nby :name\nat :at\n:tags",
    ],
    'simple_screenshot' => [
        'create' => "スクリーンショット投稿『:title』\nby :name",
    ],
];
