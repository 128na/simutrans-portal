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
    public function __construct(Screenshot $screenshot)
    {
        parent::__construct($screenshot);
    }

    /**
     * @return LengthAwarePaginator<Screenshot>
     */
    public function paginatePublish(): LengthAwarePaginator
    {
        return $this->model
            ->publish()
            ->with(['user', 'attachments', 'articles'])
            ->orderBy('published_at', 'desc')
            ->paginate(20);
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
     * @param  array<int,array{id:int,caption:?string,order:?int}>  $attachmentsData
     */
    public function syncAttachmentsWith(Screenshot $screenshot, array $attachmentsData): void
    {
        $collection = collect($attachmentsData);
        // add
        $attachments = $screenshot->user->myAttachments()->find($collection->pluck('id'));
        foreach ($attachments as $index => $attachment) {
            $data = $collection->first(fn ($d): bool => $d['id'] === $attachment->id);
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

    /**
     * 記事を関連付ける.
     *
     * @param  array<int|string>  $articleIds
     */
    public function syncArticles(Screenshot $screenshot, array $articleIds): void
    {
        $result = $screenshot->articles()->sync(Article::find($articleIds));
        logger('[ScreenshotRepository] syncArticles', $result);
    }
}
