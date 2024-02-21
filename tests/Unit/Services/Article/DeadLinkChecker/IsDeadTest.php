<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Article\DeadLinkChecker;

use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Services\Article\DeadLinkChecker;
use App\Services\Article\GetHeadersHandler;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class IsDeadTest extends UnitTestCase
{
    private function getSUT(): DeadLinkChecker
    {
        return app(DeadLinkChecker::class);
    }

    public function test_ok()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy']));
        });
        $this->mock(GetHeadersHandler::class, static function (MockInterface $m) {
            $m->shouldNotReceive('getHeaders')->once()->andReturn(['Status Code: 200 OK']);
        });

        $actual = $this->getSUT()->isDead($article);

        $this->assertFalse($actual);
    }

    public function test_2回まで失敗OK()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy']));
        });
        $this->mock(GetHeadersHandler::class, static function (MockInterface $m) {
            $m->shouldNotReceive('getHeaders')
                ->times(2)->andReturn(['Status Code: 500 Internal Server Error'])
                ->once()->andReturn(['Status Code: 200 OK']);
        });

        $actual = $this->getSUT()->isDead($article);

        $this->assertFalse($actual);
    }

    public function test_3回失敗でNG()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy']));
        });

        Event::fake();
        $this->mock(GetHeadersHandler::class, static function (MockInterface $m) {
            $m->shouldNotReceive('getHeaders')->times(3)->andReturn(['Status Code: 500 Internal Server Error']);
        });

        $actual = $this->getSUT()->isDead($article);

        Event::assertDispatched(DeadLinkDetected::class);
        $this->assertTrue($actual);
    }
}
