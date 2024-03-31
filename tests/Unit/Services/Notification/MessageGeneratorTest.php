<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Notification;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\Notification\MessageGenerator;
use Carbon\Carbon;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class MessageGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): MessageGenerator
    {
        return new MessageGenerator(Carbon::create(2000, 1, 2, 3, 4, 5));
    }

    /**
     * @return Article&MockInterface
     */
    private function getMockArticle()
    {
        return $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows()->getAttribute('title')->andReturn('dummy_title');
            $mock->allows()->getAttribute('slug')->andReturn('dummy_slug');
            $mock->allows()->getAttribute('user_id')->andReturn(1);
            $mock->allows()->offsetExists('user')->andReturn(true);
            $mock->allows()->getAttribute('user')->andReturn($this->mock(User::class, function (MockInterface $mock): void {
                $mock->allows()->offsetExists('nickname')->andReturn(false);
                $mock->allows()->getAttribute('name')->andReturn('dummy_name');
                $mock->allows()->getRouteKey();
            }));
            $mock->allows()->getAttribute('title')->andReturn('dummy_title');
            $mock->allows()->getAttribute('categoryPaks')->andReturn(collect([
                $this->mock(Category::class, function (MockInterface $mock): void {
                    $mock->allows()->offsetExists('slug')->andReturn(true);
                    $mock->allows()->offsetGet('slug')->andReturn('64');
                }),
            ]));
        });
    }

    public function testBuildPublishedMessage(): void
    {
        $now = '2000/01/02 03:04';
        $actual = $this->getSUT()->buildPublishedMessage($this->getMockArticle());
        $url = config('app.url');
        $expected = "新規投稿「dummy_title」\n{$url}/users/1/dummy_slug\nby dummy_name\nat {$now}\n#Simutrans #Pak64";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildUpdatedMessage(): void
    {
        $now = '2000/01/02 03:04';
        $actual = $this->getSUT()->buildUpdatedMessage($this->getMockArticle());
        $url = config('app.url');
        $expected = "「dummy_title」更新\n{$url}/users/1/dummy_slug\nby dummy_name\nat {$now}\n#Simutrans #Pak64";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimplePublishedMessage(): void
    {
        $actual = $this->getSUT()->buildSimplePublishedMessage($this->getMockArticle());
        $expected = "新規投稿「dummy_title」\nby dummy_name";

        $this->assertEquals($expected, $actual);
    }

    public function testBuildSimpleUpdatedMessage(): void
    {
        $actual = $this->getSUT()->buildSimpleUpdatedMessage($this->getMockArticle());
        $expected = "「dummy_title」更新\nby dummy_name";

        $this->assertEquals($expected, $actual);
    }
}
