<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Events\Screenshot\ScreenshotStored;
use App\Models\User;
use App\Repositories\ScreenshotRepository;

class StoreScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    /**
     * @param  array<mixed>  $data
     */
    public function store(User $user, array $data): void
    {
        $screenshot = $this->screenshotRepository->store([
            'user_id' => $user->id,
            'title' => $data['screenshot']['title'],
            'description' => $data['screenshot']['description'],
            'links' => $data['screenshot']['links'],
            'status' => $data['screenshot']['status'],
        ]);
        $this->screenshotRepository->syncAttachmentsWith($screenshot, $data['screenshot']['attachments']);
        $articleIds = array_map(fn ($a): mixed => $a['id'], $data['screenshot']['articles']);
        $this->screenshotRepository->syncArticles($screenshot, $articleIds);

        ScreenshotStored::dispatch($screenshot);
    }
}
