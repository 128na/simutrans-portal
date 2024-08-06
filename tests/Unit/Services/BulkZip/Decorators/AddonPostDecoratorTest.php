<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip\Decorators;

use App\Enums\ArticlePostType;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Tag;
use App\Models\User;
use App\Services\BulkZip\Decorators\AddonPostDecorator;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class AddonPostDecoratorTest extends TestCase
{
    private AddonPostDecorator $addonPostDecorator;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->addonPostDecorator = new AddonPostDecorator;
    }

    public function test_canProcess_対象(): void
    {
        $article = new Article(['post_type' => ArticlePostType::AddonPost]);
        $result = $this->addonPostDecorator->canProcess($article);
        $this->assertTrue($result);
    }

    public function test_canProcess_対象外_Article(): void
    {
        $article = new Article(['post_type' => ArticlePostType::AddonIntroduction]);
        $result = $this->addonPostDecorator->canProcess($article);
        $this->assertFalse($result);
    }

    public function test_canProcess_対象外_Model(): void
    {
        $model = User::factory()->make();
        $result = $this->addonPostDecorator->canProcess($model);
        $this->assertFalse($result);
    }

    public function test_process(): void
    {
        /**
         * @var Article
         */
        $mock = $this->mock(Article::class, function (MockInterface $mock): void {
            $mock->allows()->getAttribute('has_thumbnail')->andReturn(false);
            $mock->allows()->getAttribute('id')->andReturn(1);
            $mock->allows()->getAttribute('title')->andReturn('test title');
            $mock->allows()->getAttribute('slug')->andReturn('test_slug');
            $mock->allows()->getAttribute('user_id')->andReturn(1);
            $mock->allows()->offsetExists('user')->andReturn(true);
            $mock->allows()->getAttribute('user')->andReturn($this->mock(User::class, function (MockInterface $mock): void {
                $mock->allows()->offsetExists('nickname')->andReturn(false);
                $mock->allows()->offsetExists('name')->andReturn(true);
                $mock->allows()->getAttribute('name')->andReturn('test user name');
                $mock->allows()->getRouteKey()->andReturn(1);
            }));
            $mock->allows()->getAttribute('categories')->andReturn(collect([new Category(['type' => CategoryType::Addon, 'slug' => 'example'])]));
            $mock->allows()->offsetExists('file')->andReturn(true);
            $mock->allows()->getAttribute('file')
                ->andReturn(new Attachment(['original_name' => 'test.zip', 'path' => '/test/123']));
            $mock->allows()->getAttribute('tags')->andReturn(collect([new Tag(['name' => 'test tag'])]));
            $mock->allows()->getAttribute('contents')->andReturn(new AddonIntroductionContent([
                'description' => 'test description',
                'author' => 'test author',
                'license' => 'test license',
                'thanks' => 'test thanks',
            ]));
        });
        $input = ['contents' => [], 'files' => []];
        $result = $this->addonPostDecorator->process($input, $mock);

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
        $this->assertEquals('1/test.zip', $contents[0][11][1]);
    }
}
