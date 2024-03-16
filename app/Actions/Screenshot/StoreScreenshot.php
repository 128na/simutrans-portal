<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

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
            'title' => $data['title'],
            'description' => $data['description'],
            'links' => $data['links'],
            'status' => $data['status'],
        ]);
        $this->screenshotRepository->syncAttachments($screenshot, $data['attachments']);
    }
}
