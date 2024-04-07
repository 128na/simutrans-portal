<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Enums\ScreenshotStatus;
use App\Events\Screenshot\ScreenshotUpdated;
use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;
use Carbon\CarbonImmutable;

class UpdateScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
        private readonly CarbonImmutable $now,
    ) {
    }

    /**
     * @param  array{screenshot:array{id:int,title:string,description:string,links:string[],status:string,attachments:array<int,array{id:int,caption:string,order:int}>,articles:array<int,array{id:int,title:string}>}}  $data
     */
    public function update(Screenshot $screenshot, array $data): void
    {
        $notYetPublished = is_null($screenshot->published_at);
        $updateData = [
            'title' => $data['screenshot']['title'],
            'description' => $data['screenshot']['description'],
            'links' => $data['screenshot']['links'],
            'status' => $data['screenshot']['status'],
        ];
        if ($this->shouldPublish($notYetPublished, $data)) {
            $updateData['published_at'] = $this->now->toDateTimeString();
        }

        $this->screenshotRepository->update($screenshot, $updateData);
        $this->screenshotRepository->syncAttachmentsWith($screenshot, $data['screenshot']['attachments']);

        $articleIds = array_map(fn (array $a): int => $a['id'], $data['screenshot']['articles']);
        $this->screenshotRepository->syncArticles($screenshot, $articleIds);

        ScreenshotUpdated::dispatch(
            $screenshot,
            $data['should_notify'],
            $notYetPublished
        );
    }

    /**
     * 初めて公開ステータスになった？
     */
    private function shouldPublish(bool $notYetPublished, array $data): bool
    {
        return $notYetPublished && $data['screenshot']['status'] === ScreenshotStatus::Publish->value;
    }
}
