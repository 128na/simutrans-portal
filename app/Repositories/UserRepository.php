<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticleStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function __construct(public User $model) {}

    /**
     * マイページのサマリ表示用
     *
     * @return object{
     *     article_count: int|null,
     *     attachment_count: int|null,
     *     total_attachment_size: int|null,
     *     total_conversion_count: int|null,
     *     total_view_count: int|null,
     *     redirect_count: int|null,
     *     tag_count: int|null
     * }
     */
    public function getSummary(User $user): object
    {
        $userId = $user->id;
        $period = now()->format('Ym');

        /** @var object{article_count: int|null, attachment_count: int|null, total_attachment_size: int|null, total_conversion_count: int|null, total_view_count: int|null, redirect_count: int|null, tag_count: int|null} $result */
        $result = DB::query()
            ->selectSub(
                DB::table('articles')
                    ->where('user_id', $userId)
                    ->whereNull('deleted_at')
                    ->selectRaw('COUNT(*)'),
                'article_count'
            )
            ->selectSub(
                DB::table('attachments')
                    ->where('user_id', $userId)
                    ->selectRaw('COUNT(*)'),
                'attachment_count'
            )
            ->selectSub(
                DB::table('attachments')
                    ->where('user_id', $userId)
                    ->selectRaw('SUM(size)'),
                'total_attachment_size'
            )
            ->selectSub(
                DB::table('mylists')
                    ->where('user_id', $userId)
                    ->selectRaw('COUNT(*)'),
                'mylist_count'
            )
            ->selectSub(
                DB::table('mylists')
                    ->where('user_id', $userId)
                    ->where('is_public', true)
                    ->selectRaw('COUNT(*)'),
                'public_mylist_count'
            )
            ->selectSub(
                DB::table('conversion_counts')
                    ->where('user_id', $userId)
                    ->where('type', 2)
                    ->where('period', $period)
                    ->selectRaw('SUM(count)'),
                'total_conversion_count'
            )
            ->selectSub(
                DB::table('view_counts')
                    ->where('user_id', $userId)
                    ->where('type', 2)
                    ->where('period', $period)
                    ->selectRaw('SUM(count)'),
                'total_view_count'
            )
            ->selectSub(
                DB::table('redirects')
                    ->where('user_id', $userId)
                    ->selectRaw('COUNT(*)'),
                'redirect_count'
            )
            ->selectSub(
                DB::table('tags')
                    ->where(function ($q) use ($userId): void {
                        $q->where('created_by', $userId)
                            ->orWhere('last_modified_by', $userId);
                    })
                    ->selectRaw('COUNT(*)'),
                'tag_count'
            )
            ->firstOrFail();

        return $result;
    }

    /**
     * 投稿記事を持つユーザー一覧取得
     *
     * @return Collection<int,User>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['users.id', 'users.nickname', 'users.name'])
            ->whereExists(
                fn($q) => $q->selectRaw(1)
                    ->from('articles as a')
                    ->whereColumn('a.user_id', 'users.id')
                    ->where('a.status', ArticleStatus::Publish)
            )
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * 投稿記事を持つユーザー一覧取得（記事件数つき）
     *
     * @return Collection<int,User>
     */
    public function getForList(): Collection
    {
        return $this->model->query()
            ->select(['users.id', 'users.name', 'users.nickname', DB::raw('count(articles.id) as articles_count')])
            ->join('articles', function ($join): void {
                $join->on('articles.user_id', '=', 'users.id')
                    ->where('articles.status', ArticleStatus::Publish);
            })
            ->groupBy('users.id')
            ->orderByRaw('COUNT(articles.id) DESC')
            ->get();
    }

    /**
     * @param  mixed[]  $data
     */
    public function store(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * @param  mixed[]  $data
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * MFA設定が未完了のユーザー一覧を取得
     *
     * @return Collection<int, User>
     */
    public function findIncompleteMFAUsers(): Collection
    {
        return $this->model
            ->whereNotNull('two_factor_secret')
            ->whereNull('two_factor_confirmed_at')
            ->where('updated_at', '<', now()->subMinutes(15))
            ->get();
    }

    /**
     * idまたはnicknameでユーザーを取得
     */
    public function firstOrFailByIdOrNickname(string $userIdOrNickname): User
    {
        return $this->model
            ->where(function ($q) use ($userIdOrNickname): void {
                if (is_numeric($userIdOrNickname)) {
                    $q->where('id', (int) $userIdOrNickname);
                } else {
                    $q->where('nickname', $userIdOrNickname);
                }
            })
            ->firstOrFail();
    }
}
