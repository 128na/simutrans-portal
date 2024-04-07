<?php

declare(strict_types=1);

namespace Tests\Listeners\Screenshot;

use App\Enums\ScreenshotStatus as S;
use App\Events\Screenshot\ScreenshotStored;
use App\Listeners\Screenshot\OnScreenshotStored;
use App\Models\Screenshot;
use App\Notifications\SendScreenshotPublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class OnScreenshotStoredTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): OnScreenshotStored
    {
        return app(OnScreenshotStored::class);
    }

    #[DataProvider('data')]
    public function test(S $screenshotStatus, bool $shouldNotify, bool $expectNotify): void
    {
        /** @var Screenshot&MockInterface */
        $mock = $this->mock(Screenshot::class, function (MockInterface $mock) use ($screenshotStatus, $expectNotify): void {
            $mock->allows()->getAttribute('status')->andReturn($screenshotStatus);
            $mock->allows()->getInfoLogging()->andReturn([]);
            if ($expectNotify) {
                $mock->expects()->notify(SendScreenshotPublished::class)->once();
            } else {
                $mock->expects()->notify(SendScreenshotPublished::class)->never();
            }
        });

        $screenshotStored = new ScreenshotStored($mock, $shouldNotify);
        $result = $this->getSUT()->handle($screenshotStored);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '公開,投稿通知ON' => [S::Publish, T, T];
        yield '公開,投稿通知OFF' => [S::Publish, F, F];
        yield '非公開,投稿通知ON' => [S::Private, T, F];
        yield '非公開,投稿通知OFF' => [S::Private, F, F];
    }
}
