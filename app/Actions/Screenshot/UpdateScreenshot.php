<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;

class UpdateScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    /**
     * @param  array<mixed>  $data
     */
    public function update(Screenshot $screenshot, array $data): void
    {
        $this->screenshotRepository->update($screenshot, [
            'title' => $data['title'],
            'description' => $data['description'],
            'links' => $data['links'],
            'status' => $data['status'],
        ]);
        $this->screenshotRepository->syncAttachments($screenshot, $data['attachments']);
    }
}
