<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ScreenshotRepository;

use App\Enums\ScreenshotStatus;
use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;
use Tests\Feature\TestCase;

final class PaginatePublishTest extends TestCase
{
    private ScreenshotRepository $screenshotRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->screenshotRepository = app(ScreenshotRepository::class);
    }

    public function test(): void
    {
        Screenshot::factory()->create(['status' => ScreenshotStatus::Private]);
        $publish = Screenshot::factory()->create(['status' => ScreenshotStatus::Publish]);
        $result = $this->screenshotRepository->paginatePublish();

        $this->assertCount(1, $result);
        $this->assertSame($publish->id, $result->getCollection()[0]->id);
    }
}
