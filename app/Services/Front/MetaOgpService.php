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
            'title' => $article->title . ' - ' . Config::string('app.name'),
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
            'title' => Lang::get('category.pak.' . $name) . ' - ' . Config::string('app.name'),
            'description' => Lang::get('category.description.pak.' . $name),
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function announces(): array
    {
        return [
            'title' => 'お知らせ' . ' - ' . Config::string('app.name'),
            'description' => '運営からのお知らせです。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function pages(): array
    {
        return [
            'title' => '記事' . ' - ' . Config::string('app.name'),
            'description' => 'アドオン以外の記事です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function search(): array
    {
        return [
            'title' => '検索' . ' - ' . Config::string('app.name'),
            'description' => '記事の検索結果です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function users(): array
    {
        return [
            'title' => '投稿ユーザー一覧' . ' - ' . Config::string('app.name'),
            'description' => 'アドオン投稿や紹介記事のあるユーザー一覧です。',
        ];
    }

    /**
     * @return array{title:string,description:string}
     */
    public function social(): array
    {
        return [
            'title' => 'SNS・通知ツール' . ' - ' . Config::string('app.name'),
            'description' => '記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。',
        ];
    }

    /**
     * @return array{title:string}
     */
    public function discord(): array
    {
        return [
            'title' => 'Discord招待リンクの発行' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function login(): array
    {
        return [
            'title' => 'ログイン' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function registration(): array
    {
        return [
            'title' => 'ユーザー登録' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function resetPassword(): array
    {
        return [
            'title' => 'パスワードリセット' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function mypage(): array
    {
        return [
            'title' => 'マイページ' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function verifyEmail(): array
    {
        return [
            'title' => 'メールアドレスの検証' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function twoFactor(): array
    {
        return [
            'title' => '二要素認証の設定' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function loginHistories(): array
    {
        return [
            'title' => 'ログイン履歴' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function redirects(): array
    {
        return [
            'title' => 'リダイレクトの設定' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function invite(): array
    {
        return [
            'title' => 'リダイレクトの設定' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function profile(): array
    {
        return [
            'title' => 'プロフィールの編集' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function analytics(): array
    {
        return [
            'title' => 'アナリティクス' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function articleIndex(): array
    {
        return [
            'title' => '記事一覧' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function articleEdit(): array
    {
        return [
            'title' => '記事の編集' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function articleCreate(): array
    {
        return [
            'title' => '記事の作成' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function tags(): array
    {
        return [
            'title' => 'タグの編集' . ' - ' . Config::string('app.name'),
        ];
    }

    /**
     * @return array{title:string}
     */
    public function attachments(): array
    {
        return [
            'title' => 'ファイルの編集' . ' - ' . Config::string('app.name'),
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
