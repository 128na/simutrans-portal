<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\CategoryType;
use App\Http\Resources\Api\Front\TagDescriptionResource;
use App\Http\Resources\Api\Front\UserProfileResource;
use App\Models\Tag;
use App\Models\User;

final class FrontDescriptionService
{
    /**
     * @return array<string, array<string|UserProfileResource>>
     */
    public function user(User $user): array
    {
        return ['description' => [
            'title' => sprintf('%sさんの投稿', $user->name),
            'type' => 'profile',
            'profile' => new UserProfileResource($user),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function page(): array
    {
        return ['description' => [
            'title' => __('category.page.common'),
            'type' => 'message',
            'message' => __('category.description.page.common'),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function announces(): array
    {
        return ['description' => [
            'title' => __('category.page.announce'),
            'type' => 'message',
            'message' => __('category.description.page.announce'),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function ranking(): array
    {
        return ['description' => [
            'title' => 'アクセスランキング',
            'type' => 'message',
            'message' => '本日のアクセス数の多い記事ランキングです。',
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function category(CategoryType $categoryType, string $slug): array
    {
        if ($categoryType === CategoryType::License) {
            return ['description' => [
                'title' => sprintf('%sの投稿', __(sprintf('category.%s.%s', $categoryType->value, $slug))),
                'type' => 'message',
                'url' => __(sprintf('category.description.%s.%s', $categoryType->value, $slug)),
            ]];
        }

        return ['description' => [
            'title' => sprintf('%sの投稿', __(sprintf('category.%s.%s', $categoryType->value, $slug))),
            'type' => 'message',
            'message' => __(sprintf('category.description.%s.%s', $categoryType->value, $slug)),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug): array
    {
        return ['description' => [
            'title' => sprintf('%s、%sの投稿', __('category.pak.'.$pakSlug), __('category.addon.'.$addonSlug)),
            'type' => 'message',
            'message' => __('category.description.addon.'.$addonSlug),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function categoryPakNoneAddon(string $pakSlug): array
    {
        return ['description' => [
            'title' => sprintf('%s、%sの投稿', __('category.pak.'.$pakSlug), __('category.addon.none')),
            'type' => 'message',
            'message' => __('category.description.addon.none'),
        ]];
    }

    /**
     * @return array<string, array<string|TagDescriptionResource>>
     */
    public function tag(Tag $tag): array
    {
        return ['description' => [
            'type' => 'tag',
            'title' => sprintf('%sタグを含む投稿', $tag->name),
            'tag' => new TagDescriptionResource($tag),
        ]];
    }

    /**
     * @return array<string, array<string>>
     */
    public function search(string $word): array
    {
        return ['description' => [
            'title' => $word !== '' && $word !== '0' ? sprintf('%sの検索結果', $word) : '全ての記事',
        ]];
    }
}
