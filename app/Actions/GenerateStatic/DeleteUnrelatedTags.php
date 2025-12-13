<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DeleteUnrelatedTags
{
    public function __invoke(): void
    {
        /** @var int[] */
        $tagIds = DB::query()
            ->select('tags.id')
            ->from('tags')
            ->leftJoin('article_tag', 'tags.id', 'article_tag.tag_id')
            ->where('tags.last_modified_at', '<', now()->subDays(3))
            ->whereNull('article_tag.article_id')
            ->groupBy('tags.id')
            ->pluck('id')
            ->toArray();

        if (empty($tagIds)) {
            logger('nothing to be deleted tags');

            return;
        }

        logger('will be deleted tags', $tagIds);
        Tag::whereIn('id', $tagIds)->delete();
    }
}
