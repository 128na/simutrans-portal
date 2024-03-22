<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\Attachment;
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
            ->with(['user', 'attachments.fileInfo', 'articles'])
            ->orderBy('updated_at', 'desc')
            ->paginate(100);
    }

    /**
     * @return Collection<int,Screenshot>
     */
    public function findAllByUser(User $user): Collection
    {
        return $user
            ->screenshots()
            ->with(['attachments', 'articles'])
            ->get();
    }

    /**
     * 添付ファイルを関連付ける.
     *
     * @param  array<int|array<string,mixed>>  $attachmentsIds
     */
    public function syncAttachmentsWith(Screenshot $screenshot, array $attachmentsData): void
    {
        if ($screenshot->user) {
            $collection = collect($attachmentsData);
            // add
            $attachments = $screenshot->user->myAttachments()->find($collection->pluck('id'));
            foreach ($attachments as $index => $attachment) {
                $data = $collection->first(fn ($d) => $d['id'] === $attachment->id);
                if ($data) {
                    $attachment->fill([
                        'caption' => $data['caption'] ?? null,
                        'order' => $data['order'] ?? $index,
                    ]);
                }
            }
            $screenshot->attachments()->saveMany($attachments);
            //remove
            /** @var Collection<int,Attachment> */
            $shouldDetach = $screenshot->attachments()->whereNotIn('id', $collection->pluck('id'))->get();
            foreach ($shouldDetach as $attachment) {
                $attachment->attachmentable()->disassociate()->save();
            }
        }
    }

    /**
     * 記事を関連付ける.
     *
     * @param  array<int|string>  $articleIds
     */
    public function syncArticles(Screenshot $screenshot, array $articleIds): void
    {
        $result = $screenshot->articles()->sync(Article::find($articleIds));
        logger('syncArticles', $result);
    }
}
