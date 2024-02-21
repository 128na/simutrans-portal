<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Article\DeadLinkChecker;

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

    public function test()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy']));
        });

        $actual = $this->getSUT()->shouldProcess($article);

        $this->assertTrue($actual);
    }

    public function test_除外指定時はfalse()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'dummy', 'exclude_link_check' => true]));
        });

        $actual = $this->getSUT()->shouldProcess($article);

        $this->assertFalse($actual);
    }

    public function test_ブラックリストドメインはfalse()
    {
        /**
         * @var Article
         */
        $article = $this->mock(Article::class, static function (MockInterface $m) {
            $m->allows('getAttribute')
                ->withArgs(['contents'])
                ->andReturn(new AddonIntroductionContent(['link' => 'https://getuploader.com/dummy']));
        });

        $actual = $this->getSUT()->shouldProcess($article);

        $this->assertFalse($actual);
    }
}
