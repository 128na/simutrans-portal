<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip\Decorators;

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

    public function test_canProcess_対象()
    {
        $model = new Article(['post_type' => 'addon-introduction']);
        $result = $this->decorator->canProcess($model);
        $this->assertTrue($result);
    }

    public function test_canProcess_対象外_Article()
    {
        $model = new Article(['post_type' => 'addon-post']);
        $result = $this->decorator->canProcess($model);
        $this->assertFalse($result);
    }

    public function test_canProcess_対象外_Model()
    {
        $model = User::factory()->make();
        $result = $this->decorator->canProcess($model);
        $this->assertFalse($result);
    }

    public function test_process()
    {
        /**
         * @var Article
         */
        $model = $this->mock(Article::class, function (MockInterface $m) {
            $m->shouldReceive('getAttribute')->withArgs(['has_thumbnail'])->andReturn(false);
            $m->shouldReceive('getAttribute')->withArgs(['id'])->andReturn(1);
            $m->shouldReceive('getAttribute')->withArgs(['title'])->andReturn('test title');
            $m->shouldReceive('getAttribute')->withArgs(['slug'])->andReturn('test_slug');
            $m->shouldReceive('offsetExists')->withArgs(['user'])->andReturn(true);
            $m->shouldReceive('getAttribute')->withArgs(['user_id'])->andReturn(1);
            $m->shouldReceive('getAttribute')->withArgs(['user'])->andReturn($this->mock(User::class, function (MockInterface $m) {
                $m->shouldReceive('offsetExists')->withArgs(['nickname'])->andReturn(false);
                $m->shouldReceive('offsetExists')->withArgs(['name'])->andReturn(true);
                $m->shouldReceive('getAttribute')->withArgs(['name'])->andReturn('test user name');
                $m->shouldReceive('getRouteKey')->andReturn(1);
            }));
            $m->shouldReceive('getAttribute')->withArgs(['categories'])
                ->andReturn(collect([new Category(['type' => 'test', 'slug' => 'example'])]));
            $m->shouldReceive('tags')->andReturn($this->mock(BelongsToMany::class, function (MockInterface $m) {
                $m->shouldReceive('pluck')
                    ->andReturn(collect(['test tag']));
            }));
            $m->shouldReceive('getAttribute')->withArgs(['contents'])->andReturn(new AddonIntroductionContent([
                'author' => 'test author',
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
        $result = $this->decorator->process($input, $model);

        $contents = $result['contents'];

        $this->assertEquals(1, $contents[0][0][1]);
        $this->assertEquals('test title', $contents[0][1][1]);
        $this->assertEquals(route('articles.show', ['userIdOrNickname' => 1, 'articleSlug' => 'test_slug']), $contents[0][2][1]);
        $this->assertEquals('無し', $contents[0][3][1]);
        $this->assertEquals('test user name', $contents[0][4][1]);
        $this->assertEquals('category.test.example', $contents[0][5][1]);
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
