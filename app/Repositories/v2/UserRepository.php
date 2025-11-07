<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Enums\ArticleStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class UserRepository
{
    public function __construct(public User $model) {}

    /**
     * @param User $user
     * @return object{
     *     article_count: int|null,
     *     attachment_count: int|null,
     *     total_attachment_size: int|null,
     *     total_conversion_count: int|null,
     *     total_view_count: int|null,
     *     tag_count: int|null
     * }
     */
    public function getSummary(User $user): object
    {
        $userId = $user->id;
        $period = now()->format('Ym');
        return DB::query()
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
                DB::table('tags')
                    ->where(function ($q) use ($userId) {
                        $q->where('created_by', $userId)
                            ->orWhere('last_modified_by', $userId);
                    })
                    ->selectRaw('COUNT(*)'),
                'tag_count'
            )
            ->first();
    }

    /**
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
     * @return Collection<int,User>
     */
    public function getForList(): Collection
    {
        return $this->model->query()
            ->select(['users.id', 'users.name'])
            ->join('articles', function ($join): void {
                $join->on('articles.user_id', '=', 'users.id')
                    ->where('articles.status', ArticleStatus::Publish);
            })
            ->groupBy('users.id')
            ->orderByRaw('COUNT(articles.id) DESC')
            ->withCount('articles')
            ->get();
    }

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     role: \App\Enums\UserRole::User,
     *     password: string,
     *     invited_by?: int,
     * } $data
     */
    public function store(array $data): User
    {
        return $this->model->create($data);
    }
}
