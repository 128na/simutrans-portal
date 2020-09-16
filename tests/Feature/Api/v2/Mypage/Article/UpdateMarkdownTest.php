<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateMarkdownTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testValidation()
    {
        $user = User::factory()->create();
        $article = $this->createMarkdown($user);
        $url = route('api.v2.articles.update', $article);

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'markdown',
            'status' => 'publish',
            'title' => 'test title ' . $date,
            'slug' => 'test-slug-' . $date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'markdown' => '# hello',
            ],
            'categories' => [
                Category::page()->first()->id,
            ],
        ];

        // ステータスが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['status' => ''])]);
        $res->assertJsonValidationErrors(['article.status']);
        // 不正なステータス
        $res = $this->postJson($url, ['article' => array_merge($data, ['status' => 'test_example'])]);
        $res->assertJsonValidationErrors(['article.status']);

        // タイトルが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['title' => ''])]);
        $res->assertJsonValidationErrors(['article.title']);
        // タイトルが256文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['title' => str_repeat('a', 256)])]);
        $res->assertJsonValidationErrors(['article.title']);
        // タイトルが重複
        $other_article = Article::factory()->create(['user_id' => User::factory()->create()->id]);
        $res = $this->postJson($url, ['article' => array_merge($data, ['title' => $other_article->title])]);
        $res->assertJsonValidationErrors(['article.title']);

        // スラッグが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['slug' => ''])]);
        $res->assertJsonValidationErrors(['article.slug']);
        // スラッグが256文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['slug' => str_repeat('a', 256)])]);
        $res->assertJsonValidationErrors(['article.slug']);
        // スラッグが重複
        $other_article = Article::factory()->create(['user_id' => User::factory()->create()->id]);
        $res = $this->postJson($url, ['article' => array_merge($data, ['slug' => $other_article->slug])]);
        $res->assertJsonValidationErrors(['article.slug']);

        // 存在しないサムネイルID
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => 99999]])]);
        $res->assertJsonValidationErrors(['article.contents.thumbnail']);
        // 画像以外
        $file_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), $user->id);
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => $file_attachment->id]])]);
        $res->assertJsonValidationErrors(['article.contents.thumbnail']);
        // 他人の投稿したサムネイルID
        $others_attachment = Attachment::createFromFile(UploadedFile::fake()->image('other.png', 1), User::factory()->create()->id);
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => $others_attachment->id]])]);
        $res->assertJsonValidationErrors(['article.contents.thumbnail']);

        // markdownが無い
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['markdown' => null]])]);
        $res->assertJsonValidationErrors(['article.contents.markdown']);
        // markdownが65536文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['markdown' => \str_repeat('a', 65536)]])]);
        $res->assertJsonValidationErrors(['article.contents.markdown']);

        // カテゴリが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['categories' => null])]);
        $res->assertJsonValidationErrors(['article.categories']);

        // 存在しないカテゴリ
        $res = $this->postJson($url, ['article' => array_merge($data, ['categories' => [99999]])]);
        $res->assertJsonValidationErrors(['article.categories.0']);

        // 適切なデータ
        $res = $this->postJson($url, ['article' => $data]);
        $res->assertStatus(200);
        $get_response = json_decode($this->getJson(route('api.v2.articles.index'))->content(), true);
        $res->assertJson($get_response);
    }

    public function testPermission()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $other_user = User::factory()->create();
        $other_article = $this->createMarkdown($other_user);
        $url = route('api.v2.articles.update', $other_article);

        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function testPreview()
    {
        $user = User::factory()->create();
        $article = $this->createMarkdown($user);
        $url = route('api.v2.articles.update', $article);
        $this->actingAs($user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'markdown',
            'status' => 'publish',
            'title' => 'test title ' . $date,
            'slug' => 'test-slug-' . $date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'markdown' => '# hello',
            ],
            'categories' => [
                Category::page()->first()->id,
            ],
        ];
        $res = $this->postJson($url, ['article' => $data, 'preview' => true]);
        $res->assertHeader('content-type', 'text/html; charset=UTF-8');
        $res->assertSee('<html', false);
        $res->assertSee($data['title']);
        $this->assertDatabaseMissing('articles', [
            'title' => $data['title'],
        ]);
    }
}
