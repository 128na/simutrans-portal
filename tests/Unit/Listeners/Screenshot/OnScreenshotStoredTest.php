<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Screenshot;

use App\Enums\ScreenshotStatus as S;
use App\Events\Screenshot\ScreenshotStored;
use App\Listeners\Screenshot\OnScreenshotStored;
use App\Models\Screenshot;
use App\Notifications\SendScreenshotPublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

final class OnScreenshotStoredTest extends TestCase
{
    private function getSUT(): OnScreenshotStored
    {
        return app(OnScreenshotStored::class);
    }

    #[DataProvider('data')]
    public function test(S $s, bool $shouldNotify, bool $expectNotify): void
    {
        /** @var Screenshot&MockInterface */
        $mock = $this->mock(Screenshot::class, function (MockInterface $mock) use ($s, $expectNotify): void {
            $mock->allows()->getAttribute('status')->andReturn($s);
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
        yield '公開,通知ON->投稿通知' => [S::Publish, true, true];
        yield '公開,通知OFF->通知しない' => [S::Publish, false, false];
        yield '非公開,通知ON->通知しない' => [S::Private, true, false];
        yield '非公開,通知OFF->通知しない' => [S::Private, false, false];
    }
}
