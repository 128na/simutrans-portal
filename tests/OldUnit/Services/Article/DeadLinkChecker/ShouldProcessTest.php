<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\Article\DeadLinkChecker;

use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Services\Article\DeadLinkChecker;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class ShouldProcessTest extends UnitTestCase
{
    private function getSUT(): DeadLinkChecker
    {
        return app(DeadLinkChecker::class);
    }

    public function test(): void
    {
        /**
         * @var Article
         */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy']));
        });

        $actual = $this->getSUT()->shouldProcess($mock);

        $this->assertTrue($actual);
    }

    public function test_除外指定時はfalse(): void
    {
        /**
         * @var Article
         */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => true]));
        });

        $actual = $this->getSUT()->shouldProcess($mock);

        $this->assertFalse($actual);
    }

    public function test_ブラックリストドメインはfalse(): void
    {
        /**
         * @var Article
         */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'https://getuploader.com/dummy']));
        });

        $actual = $this->getSUT()->shouldProcess($mock);

        $this->assertFalse($actual);
    }
}
