<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\Notification;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
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

    public function testBuildPublishedMessage(): void
    {
        /** @var Article */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $mock->allows('getAttribute')->withArgs(['slug'])->andReturn('dummy_slug');
            $mock->allows('offsetExists')->withArgs(['user'])->andReturn(true);
            $mock->allows('getAttribute')->withArgs(['user_id'])->andReturn(1);
            $mock->allows('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $mock): void {
                $mock->allows('offsetExists')->withArgs(['nickname'])->andReturn(false);
                $mock->allows('getAttribute')->withArgs(['name'])->andReturn('dummy_name');
                $mock->allows('getRouteKey')->andReturn(1);
            }));
            $mock->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $mock->allows('getAttribute')->withArgs(['categoryPaks'])->andReturn(collect([
                $this->mock(Category::class, function (MockInterface $mock): void {
                    $mock->allows('offsetExists')->withArgs(['slug'])->andReturn(true);
                    $mock->allows('offsetGet')->withArgs(['slug'])->andReturn('64');
                }),
            ]));
        });
        $now = now()->format('Y/m/d H:i');
        $actual = $this->getSUT()->buildPublishedMessage($mock);
        $url = config('app.url');
        $expected = "新規投稿「dummy_title」\n{$url}/users/1/dummy_slug\nby dummy_name\nat {$now}\n#Simutrans #Pak64";

        $this->assertEquals($expected, $actual);
    }

    private function getMockArticle()
    {
        return $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $mock->allows('getAttribute')->withArgs(['slug'])->andReturn('dummy_slug');
            $mock->allows('getAttribute')->withArgs(['user_id'])->andReturn(1);
            $mock->allows('offsetExists')->withArgs(['user'])->andReturn(true);
            $mock->allows('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $mock): void {
                $mock->allows('offsetExists')->withArgs(['nickname'])->andReturn(false);
                $mock->allows('getAttribute')->withArgs(['name'])->andReturn('dummy_name');
                $mock->allows('getRouteKey')->andReturn(1);
            }));
            $mock->allows('getAttribute')->withArgs(['title'])->andReturn('dummy_title');
            $mock->allows('getAttribute')->withArgs(['categoryPaks'])->andReturn(collect([
                $this->mock(Category::class, function (MockInterface $mock): void {
                    $mock->allows('offsetExists')->withArgs(['slug'])->andReturn(true);
                    $mock->allows('offsetGet')->withArgs(['slug'])->andReturn('64');
                }),
            ]));
        });
    }

    public function testBuildUpdatedMessage(): void
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $now = now()->format('Y/m/d H:i');
        $actual = $this->getSUT()->buildUpdatedMessage($article);
        $url = config('app.url');
        $expected = "「dummy_title」更新\n{$url}/users/1/dummy_slug\nby dummy_name\nat {$now}\n#Simutrans #Pak64";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimplePublishedMessage(): void
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $actual = $this->getSUT()->buildSimplePublishedMessage($article);
        $expected = "新規投稿「dummy_title」\nby dummy_name";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimpleUpdatedMessage(): void
    {
        /** @var Article */
        $article = $this->getMockArticle();
        $actual = $this->getSUT()->buildSimpleUpdatedMessage($article);
        $expected = "「dummy_title」更新\nby dummy_name";

        $this->assertEquals($expected, $actual);
    }
}
