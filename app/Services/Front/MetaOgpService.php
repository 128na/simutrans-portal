<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Config;

final class MetaOgpService
{
    /**
     * @return array{title:string,description:string,image:string|null,canonical:string,card_type:string}
     */
    public function show(User $user, Article $article): array
    {
        return [
            'title' => $article->title.' - '.Config::string('app.name'),
            'description' => $this->trimDescription($article->contents->getDescription()),
            'image' => $article->has_thumbnail ? $article->thumbnail_url : null,
            'canonical' => route('articles.show', ['userIdOrNickname' => $user->nickname ?? $user->id, 'articleSlug' => $article->slug]),
            'card_type' => 'summary_large_image',
        ];
    }

    /**
     * @return array{title:string,description:string,image:string|null,card_type:string}
     */
    public function user(User $user): array
    {
        return [
            'title' => sprintf('%sさんの投稿', $user->name).' - '.Config::string('app.name'),
            'description' => $this->trimDescription($user->profile?->data->description),
            'image' => $user->profile?->avatar_url,
            'card_type' => 'summary_large_image',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function category(CategoryType $categoryType, string $slug): array
    {
        if ($categoryType === CategoryType::License) {
            return [
                'title' => sprintf('%sの投稿', __(sprintf('category.%s.%s', $categoryType->value, $slug))).' - '.Config::string('app.name'),
                'description' => sprintf('%sの投稿', __(sprintf('category.%s.%s', $categoryType->value, $slug))),
            ];
        }

        return [
            'title' => sprintf('%sの投稿', __(sprintf('category.%s.%s', $categoryType->value, $slug))).' - '.Config::string('app.name'),
            'description' => __(sprintf('category.description.%s.%s', $categoryType->value, $slug)),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function categoryPakAddon(string $pakSlug, string $addonSlug): array
    {
        return [
            'title' => sprintf('%s、%sの投稿', __('category.pak.'.$pakSlug), __('category.addon.'.$addonSlug)).' - '.Config::string('app.name'),
            'description' => __('category.description.addon.'.$addonSlug),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function categoryPakNoneAddon(string $pakSlug): array
    {
        return [
            'title' => sprintf('%s、%sの投稿', __('category.pak.'.$pakSlug), __('category.addon.none')).' - '.Config::string('app.name'),
            'description' => __('category.description.addon.none'),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function tag(Tag $tag): array
    {
        return [
            'title' => sprintf('%sタグを含む投稿', $tag->name).' - '.Config::string('app.name'),
            'description' => $this->trimDescription($tag->description),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function social(): array
    {
        return [
            'title' => 'SNS・通知ツール'.' - '.Config::string('app.name'),
            'description' => '記事の更新を各種ツールで受け取れます。',
        ];
    }

    private function trimDescription(?string $str): string
    {
        if (in_array($str, [null, '', '0'], true)) {
            return Config::string('app.meta-description');
        }

        $str = str_replace(["\n", "\r"], '', $str);

        return mb_strimwidth($str, 0, 200, '…');
    }
}
