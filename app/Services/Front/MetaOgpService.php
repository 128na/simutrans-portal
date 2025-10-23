<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

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
     * @return array{title:string,description:string}
     */
    public function pak(string $name): array
    {
        return [
            'title' => Lang::get('category.pak.'.$name).' - '.Config::string('app.name'),
            'description' => Lang::get('category.description.pak.'.$name),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function announce(): array
    {
        return [
            'title' => 'お知らせ'.' - '.Config::string('app.name'),
            'description' => '運営からのお知らせです。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function search(): array
    {
        return [
            'title' => '検索'.' - '.Config::string('app.name'),
            'description' => '記事の検索結果です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function social(): array
    {
        return [
            'title' => 'SNS・通知ツール'.' - '.Config::string('app.name'),
            'description' => '記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。',
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
