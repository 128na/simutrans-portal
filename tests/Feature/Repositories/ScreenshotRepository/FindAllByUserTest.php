<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ScreenshotRepository;

use App\Enums\ScreenshotStatus;
use App\Models\Screenshot;
use App\Models\User;
use App\Repositories\ScreenshotRepository;
use Tests\Feature\TestCase;

final class FindAllByUserTest extends TestCase
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
        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create(['user_id' => $user->id, 'status' => ScreenshotStatus::Private]);
        Screenshot::factory()->create(['status' => ScreenshotStatus::Publish]);
        $result = $this->screenshotRepository->findAllByUser($user);

        $this->assertCount(1, $result);
        $this->assertSame($screenshot->id, $result[0]->id);
    }
}
