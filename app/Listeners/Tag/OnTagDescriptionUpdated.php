<?php

declare(strict_types=1);

namespace App\Listeners\Tag;

use App\Events\Tag\TagDescriptionUpdated;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnTagDescriptionUpdated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(TagDescriptionUpdated $tagDescriptionUpdated): void
    {
        $this->logger->channel('audit')->info('タグ説明更新', array_merge(
            $tagDescriptionUpdated->user->getInfoLogging(),
            $tagDescriptionUpdated->tag->getInfoLogging(),
            ['old' => $tagDescriptionUpdated->old, 'new' => $tagDescriptionUpdated->tag->description]
        ));
    }
}
