<?php

declare(strict_types=1);

namespace App\Listeners\Tag;

use App\Events\Tag\TagDescriptionUpdated;
use Illuminate\Log\Logger;

final readonly class OnTagDescriptionUpdated
{
    public function __construct(
        private Logger $logger,
    ) {}

    public function handle(TagDescriptionUpdated $tagDescriptionUpdated): void
    {
        $this->logger->channel('audit')->info('タグ説明更新', array_merge(
            $tagDescriptionUpdated->user->getInfoLogging(),
            $tagDescriptionUpdated->tag->getInfoLogging(),
            ['old' => $tagDescriptionUpdated->old, 'new' => $tagDescriptionUpdated->tag->description],
        ));
    }
}
