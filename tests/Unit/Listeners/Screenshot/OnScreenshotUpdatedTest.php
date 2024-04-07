<?php

declare(strict_types=1);

namespace Tests\Listeners\Screenshot;

use App\Enums\ScreenshotStatus as S;
use App\Events\Screenshot\ScreenshotUpdated;
use App\Listeners\Screenshot\OnScreenshotUpdated;
use App\Models\Screenshot;
use App\Notifications\SendScreenshotPublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class OnScreenshotUpdatedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): OnScreenshotUpdated
    {
        return app(OnScreenshotUpdated::class);
    }

    #[DataProvider('data')]
    public function test(
        bool $notYetPublished,
        S $s,
        bool $shouldNotify,
        bool $expectNotify
    ): void {
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

        $screenshotUpdated = new ScreenshotUpdated($mock, $shouldNotify, $notYetPublished);
        $result = $this->getSUT()->handle($screenshotUpdated);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '未公開,公開,投稿通知ON' => [true, S::Publish, true, true];
        yield '未公開,公開,投稿通知OF' => [true, S::Publish, false, false];
        yield '未公開,非公開,投稿通知ON' => [true, S::Private, true, false];
        yield '未公開,非公開,投稿通知OF' => [true, S::Private, false, false];
        yield '公開済,公開,投稿通知ON' => [false, S::Publish, true, false];
        yield '公開済,公開,投稿通知OF' => [false, S::Publish, false, false];
        yield '公開済,非公開,投稿通知ON' => [false, S::Private, true, false];
        yield '公開済,非公開,投稿通知OF' => [false, S::Private, false, false];

    }
}
