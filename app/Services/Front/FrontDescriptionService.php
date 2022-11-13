<?php

namespace App\Services\Front;

use App\Http\Resources\Api\Front\TagDescriptionResource;
use App\Http\Resources\Api\Front\UserProfileResource;
use App\Models\Tag;
use App\Models\User;

class FrontDescriptionService
{
    public function user(User $user): array
    {
        return ['description' => [
            'title' => sprintf('%sさんの投稿', $user->name),
            'type' => 'profile',
            'profile' => new UserProfileResource($user),
        ]];
    }

    public function page(): array
    {
        return ['description' => [
            'title' => __('category.page.common'),
            'type' => 'message',
            'message' => __('category.description.page.common'),
        ]];
    }

    public function announces(): array
    {
        return ['description' => [
            'title' => __('category.page.announce'),
            'type' => 'message',
            'message' => __('category.description.page.announce'),
        ]];
    }

    public function ranking(): array
    {
        return ['description' => [
            'title' => 'アクセスランキング',
            'type' => 'message',
            'message' => '本日のアクセス数の多い記事ランキングです。',
        ]];
    }

    public function category(string $type, string $slug): array
    {
        if ($type === 'license') {
            return ['description' => [
                'title' => sprintf('%sの投稿', __("category.{$type}.{$slug}")),
                'type' => 'message',
                'url' => __("category.description.{$type}.{$slug}"),
            ]];
        }

        return ['description' => [
            'title' => sprintf('%sの投稿', __("category.{$type}.{$slug}")),
            'type' => 'message',
            'message' => __("category.description.{$type}.{$slug}"),
        ]];
    }

    public function categoryPakAddon(string $pakSlug, string $addonSlug): array
    {
        return ['description' => [
            'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __("category.addon.{$addonSlug}")),
            'type' => 'message',
            'message' => __("category.description.addon.{$addonSlug}"),
        ]];
    }

    public function categoryPakNoneAddon(string $pakSlug): array
    {
        return ['description' => [
            'title' => sprintf('%s、%sの投稿', __("category.pak.{$pakSlug}"), __('category.addon.none')),
            'type' => 'message',
            'message' => __('category.description.addon.none'),
        ]];
    }

    public function tag(Tag $tag): array
    {
        return ['description' => [
            'type' => 'tag',
            'title' => sprintf('%sタグを含む投稿', $tag->name),
            'tag' => new TagDescriptionResource($tag),
        ]];
    }
}
