<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\Service;

class MetaOgpService extends Service
{
    public function __construct()
    {
    }

    /**
     * @return array<string, string|null>
     */
    public function show(Article $article): array
    {
        return [
            'title' => $article->title.' - '.config('app.name'),
            'description' => $this->trimDescription($article->contents->getDescription()),
            'image' => $article->has_thumbnail ? $article->thumbnail_url : null,
            'canonical' => route('articles.show', $article->slug),
            'card_type' => 'summary_large_image',
        ];
    }

    private function trimDescription(?string $str): string
    {
        if (! $str) {
            return config('app.meta-description');
        }
        $str = str_replace(["\n", "\r"], '', $str);
        $str = mb_strimwidth($str, 0, 200, '…');

        return $str;
    }

    /**
     * @return array<string, string|null>
     */
    public function user(User $user): array
    {
        return [
            'title' => sprintf('%sさんの投稿', $user->name).' - '.config('app.name'),
            'description' => $this->trimDescription($user->profile?->data->description),
            'image' => $user->profile?->avatar_url,
            'card_type' => 'summary_large_image',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function category(string $type, string $slug): array
    {
        if ($type === 'license') {
            return [
                'title' => sprintf('%sの投稿', __("category.{$type}.{$slug}")).' - '.config('app.name'),
                'description' => sprintf('%sの投稿', __("category.{$type}.{$slug}")),
            ];
        }

        return [
            'title' => sprintf('%sの投稿', __("category.{$type}.{$slug}")).' - '.config('app.name'),
            'description' => __("category.description.{$type}.{$slug}"),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug): array
    {
        return [
            'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __("category.addon.{$addonSlug}")).' - '.config('app.name'),
            'description' => __("category.description.addon.{$addonSlug}"),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function categoryPakNoneAddon(string $pakSlug): array
    {
        return [
            'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __('category.addon.none')).' - '.config('app.name'),
            'description' => __('category.description.addon.none'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function tag(Tag $tag): array
    {
        return [
            'title' => sprintf('%sタグを含む投稿', $tag->name).' - '.config('app.name'),
            'description' => $this->trimDescription($tag->description),
        ];
    }
}
