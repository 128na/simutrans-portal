<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Screenshot;

use App\Actions\Screenshot\StoreScreenshot;
use App\Enums\ScreenshotStatus;
use App\Events\Screenshot\ScreenshotStored;
use App\Models\Screenshot;
use App\Models\User;
use App\Repositories\ScreenshotRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class StoreScreenshotTest extends TestCase
{
    public function test非公開(): void
    {
        $data = [
            'should_notify' => false,
            'screenshot' => [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
                'attachments' => [],
                'articles' => [],
            ],
        ];
        /** @var User&MockInterface */
        $user = $this->mock(User::class, function (MockInterface $mock): void {
            $mock->allows()->getAttribute('id')->andReturn(1);
        });
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock): void {
            $screenshot = new Screenshot;
            $mock->expects()->store([
                'user_id' => 1,
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
                'published_at' => null,
            ])->once()->andReturn($screenshot);
            $mock->expects()->syncAttachmentsWith($screenshot, []);
            $mock->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->store($user, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotStored::class, fn (ScreenshotStored $screenshotStored): bool => $screenshotStored->shouldNotify === false);
    }

    public function test公開(): void
    {
        $data = [
            'should_notify' => true,
            'screenshot' => [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
                'attachments' => [],
                'articles' => [],
            ],
        ];
        /** @var User&MockInterface */
        $user = $this->mock(User::class, function (MockInterface $mock): void {
            $mock->allows()->getAttribute('id')->andReturn(1);
        });
        $this->mock(CarbonImmutable::class, function (MockInterface $mock): void {
            $mock->expects()->toDateTimeString()->once()->andReturn('2020-01-02 03:04:05');
        });
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock): void {
            $screenshot = new Screenshot;
            $mock->expects()->store([
                'user_id' => 1,
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
                'published_at' => '2020-01-02 03:04:05',
            ])->once()->andReturn($screenshot);
            $mock->expects()->syncAttachmentsWith($screenshot, []);
            $mock->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->store($user, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotStored::class, fn (ScreenshotStored $screenshotStored): bool => $screenshotStored->shouldNotify);
    }

    private function getSUT(): StoreScreenshot
    {
        return app(StoreScreenshot::class);
    }
}
