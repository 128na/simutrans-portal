<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

final class MetaOgpService
{
    /**
     * @return array{title:string,description:string,image:string|null,canonical:string,card_type:string}
     */
    public function frontArticleShow(User $user, Article $article): array
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
    public function frontPak(string $name): array
    {
        return [
            'title' => Lang::get('category.pak.'.$name).' - '.Config::string('app.name'),
            'description' => Lang::get('category.description.pak.'.$name),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontAnnounces(): array
    {
        return [
            'title' => 'お知らせ'.' - '.Config::string('app.name'),
            'description' => '運営からのお知らせです。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontPages(): array
    {
        return [
            'title' => '記事'.' - '.Config::string('app.name'),
            'description' => 'アドオン以外の記事です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontPakAddon(Category $pak, Category $addon): array
    {
        return [
            'title' => sprintf(
                '%s / %s の記事'.' - '.Config::string('app.name'),
                __('category.pak.'.$pak->slug),
                __('category.addon.'.$addon->slug)
            ),
            'description' => sprintf(
                '%s / %s の記事一覧です。',
                __('category.pak.'.$pak->slug),
                __('category.addon.'.$addon->slug)
            ),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontPakAddonList(): array
    {
        return [
            'title' => 'Pak別アドオン一覧 - '.Config::string('app.name'),
            'description' => '主なPakごとのアドオン一覧です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontTag(Tag $tag): array
    {
        return [
            'title' => sprintf('タグ「%s」の記事', $tag->name).' - '.Config::string('app.name'),
            'description' => sprintf('タグ「%s」の記事一覧です。', $tag->name),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontTags(): array
    {
        return [
            'title' => 'タグ一覧 - '.Config::string('app.name'),
            'description' => '登録されているタグ一覧です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontUser(User $user): array
    {
        return [
            'title' => $user->name.'さんの記事'.' - '.Config::string('app.name'),
            'description' => $user->name.'さんの記事一覧です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontUsers(): array
    {
        return [
            'title' => '投稿ユーザー一覧'.' - '.Config::string('app.name'),
            'description' => 'アドオン投稿や紹介記事のあるユーザー一覧です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontSearch(): array
    {
        return [
            'title' => '検索'.' - '.Config::string('app.name'),
            'description' => '記事の検索結果です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function frontSocial(): array
    {
        return [
            'title' => 'SNS・通知ツール'.' - '.Config::string('app.name'),
            'description' => '記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。',
        ];
    }

    /**
     * @return array{title:string}
     */
    public function frontDiscord(): array
    {
        return [
            'title' => 'Discord招待リンクの発行'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageLogin(): array
    {
        return [
            'title' => 'ログイン'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageRegistration(): array
    {
        return [
            'title' => 'ユーザー登録'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageResetPassword(): array
    {
        return [
            'title' => 'パスワードリセット'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypage(): array
    {
        return [
            'title' => 'マイページ'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageVerifyEmail(): array
    {
        return [
            'title' => 'メールアドレスの検証'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageTwoFactor(): array
    {
        return [
            'title' => '二要素認証の設定'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageLoginHistories(): array
    {
        return [
            'title' => 'ログイン履歴'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageRedirects(): array
    {
        return [
            'title' => 'リダイレクトの設定'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageInvite(): array
    {
        return [
            'title' => '招待コードの発行'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageProfile(): array
    {
        return [
            'title' => 'プロフィールの編集'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageAnalytics(): array
    {
        return [
            'title' => 'アナリティクス'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageArticles(): array
    {
        return [
            'title' => '記事一覧'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageArticleEdit(): array
    {
        return [
            'title' => '記事の編集'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageArticleCreate(): array
    {
        return [
            'title' => '記事の作成'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageTags(): array
    {
        return [
            'title' => 'タグの編集'.' - '.Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypageAttachments(): array
    {
        return [
            'title' => 'ファイルの編集'.' - '.Config::string('app.name'),
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
