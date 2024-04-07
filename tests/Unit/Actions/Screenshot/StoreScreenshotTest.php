<?php

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

class StoreScreenshotTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): StoreScreenshot
    {
        return app(StoreScreenshot::class);
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
        /** @var User&MockInterface */
        $user = $this->mock(User::class, function (MockInterface $m) {
            $m->allows()->getAttribute('id')->andReturn(1);
        });
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) {
            $s = new Screenshot();
            $m->expects()->store([
                'user_id' => 1,
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Private->value,
                'links' => [],
                'published_at' => null,
            ])->once()->andReturn($s);
            $m->expects()->syncAttachmentsWith($s, []);
            $m->expects()->syncArticles($s, []);
        });

        Event::fake();
        $result = $this->getSUT()->store($user, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotStored::class, function (ScreenshotStored $e) {
            return $e->shouldNotify === false;
        });
    }

    public function test公開()
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
        $user = $this->mock(User::class, function (MockInterface $m) {
            $m->allows()->getAttribute('id')->andReturn(1);
        });
        $this->mock(CarbonImmutable::class, function (MockInterface $m) {
            $m->expects()->toDateTimeString()->once()->andReturn('2020-01-02 03:04:05');
        });
        $this->mock(ScreenshotRepository::class, function (MockInterface $m) {
            $s = new Screenshot();
            $m->expects()->store([
                'user_id' => 1,
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'links' => [],
                'published_at' => '2020-01-02 03:04:05',
            ])->once()->andReturn($s);
            $m->expects()->syncAttachmentsWith($s, []);
            $m->expects()->syncArticles($s, []);
        });

        Event::fake();
        $result = $this->getSUT()->store($user, $data);
        $this->assertNull($result);
        Event::assertDispatched(ScreenshotStored::class, function (ScreenshotStored $e) {
            return $e->shouldNotify === true;
        });
    }
}
