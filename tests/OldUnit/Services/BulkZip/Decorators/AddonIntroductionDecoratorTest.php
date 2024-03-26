<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\BulkZip\Decorators;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\User;
use App\Services\BulkZip\Decorators\AddonIntroductionDecorator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class AddonIntroductionDecoratorTest extends UnitTestCase
{
    private AddonIntroductionDecorator $decorator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decorator = new AddonIntroductionDecorator();
    }

    public function test_canProcess_対象(): void
    {
        $article = new Article(['post_type' => ArticlePostType::AddonIntroduction]);
        $result = $this->decorator->canProcess($article);
        $this->assertTrue($result);
    }

    public function test_canProcess_対象外_Article(): void
    {
        $article = new Article(['post_type' => ArticlePostType::AddonPost]);
        $result = $this->decorator->canProcess($article);
        $this->assertFalse($result);
    }

    public function test_canProcess_対象外_Model(): void
    {
        $model = User::factory()->make();
        $result = $this->decorator->canProcess($model);
        $this->assertFalse($result);
    }

    public function test_process(): void
    {
        /**
         * @var Article
         */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getAttribute')->withArgs(['has_thumbnail'])->andReturn(false);
            $mock->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(1);
            $mock->shouldReceive('getAttribute')->withArgs(['title'])->andReturn('test title');
            $mock->shouldReceive('getAttribute')->withArgs(['slug'])->andReturn('test_slug');
            $mock->shouldReceive('offsetExists')->withArgs(['user'])->andReturn(true);
            $mock->shouldReceive('getAttribute')->withArgs(['user_id'])->andReturn(1);
            $mock->shouldReceive('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $mock): void {
                $mock->shouldReceive('offsetExists')->withArgs(['nickname'])->andReturn(false);
                $mock->shouldReceive('offsetExists')->withArgs(['name'])->andReturn(true);
                $mock->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('test user name');
                $mock->shouldReceive('getRouteKey')->andReturn(1);
            }));
            $mock->shouldReceive('getAttribute')->withArgs(['categories'])
                ->andReturn(collect([new Category(['type' => CategoryType::Addon, 'slug' => 'example'])]));
            $mock->shouldReceive('tags')->andReturn($this->mock(BelongsToMany::class, function (MockInterface $mock): void {
                $mock->shouldReceive('pluck')
                    ->andReturn(collect(['test tag']));
            }));
            $mock->shouldReceive('getAttribute')->withArgs(['contents'])->andReturn(new AddonIntroductionContent([
                'description' => 'test description',
                'link' => 'http://example.com',
                'author' => 'test author',
                'license' => 'test license',
                'thanks' => 'test thanks',
                'agreement' => true,
                'exclude_link_check' => true,
            ]));
        });
        $input = ['contents' => [], 'files' => []];
        $result = $this->decorator->process($input, $mock);

        $contents = $result['contents'];

        $this->assertEquals(1, $contents[0][0][1]);
        $this->assertEquals('test title', $contents[0][1][1]);
        $this->assertEquals(route('articles.show', ['userIdOrNickname' => 1, 'articleSlug' => 'test_slug']), $contents[0][2][1]);
        $this->assertEquals('無し', $contents[0][3][1]);
        $this->assertEquals('test user name', $contents[0][4][1]);
        $this->assertEquals('category.addon.example', $contents[0][5][1]);
        $this->assertEquals('test tag', $contents[0][6][1]);
        $this->assertEquals('test author', $contents[0][7][1]);
        $this->assertEquals('test description', $contents[0][8][1]);
        $this->assertEquals('test thanks', $contents[0][9][1]);
        $this->assertEquals('test license', $contents[0][10][1]);
        $this->assertEquals('Yes', $contents[0][11][1]);
        $this->assertEquals('http://example.com', $contents[0][12][1]);
        $this->assertEquals('No', $contents[0][13][1]);
    }
}
