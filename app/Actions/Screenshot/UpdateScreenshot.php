<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Events\Screenshot\ScreenshotUpdated;
use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;

class UpdateScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    /**
     * @param  array{screenshot:array{id:int,title:string,description:string,links:string[],status:string,attachments:array<int,array{id:int,caption:string,order:int}>,articles:array<int,array{id:int,title:string}>}}  $data
     */
    public function update(Screenshot $screenshot, array $data): void
    {
        $this->screenshotRepository->update($screenshot, [
            'title' => $data['screenshot']['title'],
            'description' => $data['screenshot']['description'],
            'links' => $data['screenshot']['links'],
            'status' => $data['screenshot']['status'],
        ]);
        $this->screenshotRepository->syncAttachmentsWith($screenshot, $data['screenshot']['attachments']);

        $articleIds = array_map(fn (array $a): int => $a['id'], $data['screenshot']['articles']);
        $this->screenshotRepository->syncArticles($screenshot, $articleIds);

        ScreenshotUpdated::dispatch($screenshot);
    }
}
