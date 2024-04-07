<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Screenshot;

use App\Actions\Screenshot\UpdateScreenshot;
use App\Enums\ScreenshotStatus;
use App\Events\Screenshot\ScreenshotUpdated;
use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class UpdateScreenshotTest extends TestCase
{
    private function getSUT(): UpdateScreenshot
    {
        return app(UpdateScreenshot::class);
    }

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

        $screenshot = new Screenshot([
            'status' => ScreenshotStatus::Private,
        ]);
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock) use ($screenshot): void {
            $mock->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $mock->expects()->syncAttachmentsWith($screenshot, []);
            $mock->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, fn (ScreenshotUpdated $screenshotUpdated): bool => $screenshotUpdated->shouldNotify === false);
    }

    public function test非公開から公開(): void
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
        $this->mock(CarbonImmutable::class, function (MockInterface $mock): void {
            $mock->expects()->toDateTimeString()->andReturn('2020-01-02 03:04:05');
        });
        $screenshot = new Screenshot([
            'status' => ScreenshotStatus::Private,
        ]);
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock) use ($screenshot): void {
            $mock->expects()->update($screenshot, [
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
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, fn (ScreenshotUpdated $screenshotUpdated): bool => $screenshotUpdated->shouldNotify);
    }

    public function test公開から公開(): void
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
        $screenshot = new Screenshot([
            'status' => ScreenshotStatus::Publish,
            'published_at' => '2019-01-02 03:04:05',
        ]);
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock) use ($screenshot): void {
            $mock->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $mock->expects()->syncAttachmentsWith($screenshot, []);
            $mock->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, fn (ScreenshotUpdated $screenshotUpdated): bool => $screenshotUpdated->shouldNotify);
    }

    public function test公開から非公開(): void
    {
        $data = [
            'should_notify' => true,
            'screenshot' => [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
                'attachments' => [],
                'articles' => [],
            ],
        ];
        $screenshot = new Screenshot([
            'status' => ScreenshotStatus::Publish,
            'published_at' => '2019-01-02 03:04:05',
        ]);
        $this->mock(ScreenshotRepository::class, function (MockInterface $mock) use ($screenshot): void {
            $mock->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $mock->expects()->syncAttachmentsWith($screenshot, []);
            $mock->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, fn (ScreenshotUpdated $screenshotUpdated): bool => $screenshotUpdated->shouldNotify === false);
    }
}
