<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\User\Profile;
use App\Models\User\ProfileData;
use App\Services\Notification\MessageGenerator;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class MessageGeneratorTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): MessageGenerator
    {
        return app(MessageGenerator::class);
    }

    public function testBuildPublishedMessageTwitter無し()
    {
        /** @var Article */
        $article = $this->mock(Article::class, function (MockInterface $m) {
            $m->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $m->allows('getAttribute')->withArgs(['slug'])->andReturn('dummy_slug');
            $m->allows('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $m) {
                $m->allows('getAttribute')->withArgs(['profile'])->andReturn($this->mock(Profile::class, function (MockInterface $m) {
                    $m->allows('getAttribute')->withArgs(['has_twitter'])->andReturn(false);
                }));
                $m->allows('offsetExists')->withArgs(['profile'])->andReturn(true);
                $m->allows('getAttribute')->withArgs(['name'])->andReturn('dummy_name');
            }));
            $m->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $m->allows('getAttribute')->withArgs(['categoryPaks'])->andReturn(collect([
                $this->mock(Category::class, function (MockInterface $m) {
                    $m->allows('offsetExists')->withArgs(['name'])->andReturn(true);
                    $m->allows('offsetGet')->withArgs(['name'])->andReturn('dummy_pak');
                }),
            ]));
        });
        $now = now()->format('Y/m/d H:i');
        $actual = $this->getSUT()->buildPublishedMessage($article);
        $url = config('app.url');
        $expected = "新規投稿「dummy_title」\n$url/articles/dummy_slug\nby dummy_name\nat $now\n#Simutrans #dummy_pak";

        $this->assertEquals($expected, $actual);
    }

    private function getMockArticle()
    {
        return $this->mock(Article::class, function (MockInterface $m) {
            $m->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $m->allows('getAttribute')->withArgs(['slug'])->andReturn('dummy_slug');
            $m->allows('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $m) {
                $m->allows('getAttribute')->withArgs(['profile'])->andReturn($this->mock(Profile::class, function (MockInterface $m) {
                    $m->allows('getAttribute')->withArgs(['has_twitter'])->andReturn(true);
                    $m->allows('offsetExists')->withArgs(['data'])->andReturn(true);
                    $m->allows('getAttribute')->withArgs(['data'])->andReturn(new ProfileData(['twitter' => 'dummy_twitter']));
                }));
                $m->allows('offsetExists')->withArgs(['profile'])->andReturn(true);
            }));
            $m->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $m->allows('getAttribute')->withArgs(['categoryPaks'])->andReturn(collect([
                $this->mock(Category::class, function (MockInterface $m) {
                    $m->allows('offsetExists')->withArgs(['name'])->andReturn(true);
                    $m->allows('offsetGet')->withArgs(['name'])->andReturn('dummy_pak');
                }),
            ]));
        });
    }

    public function testBuildPublishedMessage()
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $now = now()->format('Y/m/d H:i');
        $actual = $this->getSUT()->buildPublishedMessage($article);
        $url = config('app.url');
        $expected = "新規投稿「dummy_title」\n$url/articles/dummy_slug\nby @dummy_twitter\nat $now\n#Simutrans #dummy_pak";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildUpdatedMessage()
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $now = now()->format('Y/m/d H:i');
        $actual = $this->getSUT()->buildUpdatedMessage($article);
        $url = config('app.url');
        $expected = "「dummy_title」更新\n$url/articles/dummy_slug\nby @dummy_twitter\nat $now\n#Simutrans #dummy_pak";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimplePublishedMessage()
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $actual = $this->getSUT()->buildSimplePublishedMessage($article);
        $expected = "新規投稿「dummy_title」\nby @dummy_twitter";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimpleUpdatedMessage()
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $actual = $this->getSUT()->buildSimpleUpdatedMessage($article);
        $expected = "「dummy_title」更新\nby @dummy_twitter";

        $this->assertEquals($expected, $actual);
    }
}
