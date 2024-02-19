<?php

declare(strict_types=1);

namespace App\Listeners\Tag;

use App\Events\Article\TagDescriptionUpdated;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnTagDescriptionUpdated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(TagDescriptionUpdated $event): void
    {
        $this->logger->channel('audit')->info('タグ説明更新', array_merge(
            $this->getUserInfo($event->user),
            ['tagId' => $event->tag->id, 'tagName' => $event->tag->name, 'old' => $event->old, 'new' => $event->tag->description]
        ));
    }
}
