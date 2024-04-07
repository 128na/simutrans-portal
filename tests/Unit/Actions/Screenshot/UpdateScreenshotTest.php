<?php

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
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): UpdateScreenshot
    {
        return app(UpdateScreenshot::class);
    }

    public function test非公開()
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
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) use ($screenshot) {
            $m->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $m->expects()->syncAttachmentsWith($screenshot, []);
            $m->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, function (ScreenshotUpdated $e) {
            return $e->shouldNotify === false;
        });
    }

    public function test非公開から公開()
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
        $this->mock(CarbonImmutable::class, function (MockInterface $m) {
            $m->expects()->toDateTimeString()->andReturn('2020-01-02 03:04:05');
        });
        $screenshot = new Screenshot([
            'status' => ScreenshotStatus::Private,
        ]);
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) use ($screenshot) {
            $m->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
                'published_at' => '2020-01-02 03:04:05',
            ])->once()->andReturn($screenshot);
            $m->expects()->syncAttachmentsWith($screenshot, []);
            $m->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, function (ScreenshotUpdated $e) {
            return $e->shouldNotify === true;
        });
    }

    public function test公開から公開()
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
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) use ($screenshot) {
            $m->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $m->expects()->syncAttachmentsWith($screenshot, []);
            $m->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, function (ScreenshotUpdated $e) {
            return $e->shouldNotify === true;
        });
    }

    public function test公開から非公開()
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
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) use ($screenshot) {
            $m->expects()->update($screenshot, [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
            ])->once()->andReturn($screenshot);
            $m->expects()->syncAttachmentsWith($screenshot, []);
            $m->expects()->syncArticles($screenshot, []);
        });

        Event::fake();
        $result = $this->getSUT()->update($screenshot, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotUpdated::class, function (ScreenshotUpdated $e) {
            return $e->shouldNotify === false;
        });
    }
}
