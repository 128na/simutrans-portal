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
            'title' => $data['screenshot']['title'],
            'description' => $data['screenshot']['description'],
            'links' => $data['screenshot']['links'],
            'status' => $data['screenshot']['status'],
        ]);
        $this->screenshotRepository->syncAttachments($screenshot, $data['screenshot']['attachments']);
        $articleIds = array_map(fn ($a): mixed => $a['id'], $data['screenshot']['articles']);
        $this->screenshotRepository->syncArticles($screenshot, $articleIds);
    }
}
