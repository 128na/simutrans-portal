<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Screenshot>
 */
class ScreenshotRepository extends BaseRepository
{
    /**
     * @var Screenshot
     */
    protected $model;

    public function __construct(Screenshot $screenshot)
    {
        $this->model = $screenshot;
    }

    /**
     * @return LengthAwarePaginator<Screenshot>
     */
    public function paginatePublish(): LengthAwarePaginator
    {
        return $this->model
            ->publish()
            ->paginate(100);
    }

    /**
     * @return Collection<int,Screenshot>
     */
    public function findAllByUser(User $user): Collection
    {
        return $user->screenshots()->get();
    }

    /**
     * 添付ファイルを関連付ける.
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncAttachments(Screenshot $screenshot, array $attachmentsIds): void
    {
        if ($screenshot->user) {
            $attachments = $screenshot->user->myAttachments()->find($attachmentsIds);
            $screenshot->attachments()->saveMany($attachments);
        }
    }

    /**
     * 記事を関連付ける.
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncArticles(Screenshot $screenshot, array $articleIds): void
    {
        $result = $screenshot->articles()->sync(Article::find($articleIds));
        logger('syncArticles', $result);
    }
}
