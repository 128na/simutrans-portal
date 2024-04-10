<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use App\Models\Tag;

final class DeleteUnrelatedTags
{
    public function __invoke(): void
    {
        /** @var int[] */
        $tagIds = Tag::doesntHave('articles')
            ->pluck('id');

        Tag::whereIn('id', $tagIds)->delete();
    }
}
