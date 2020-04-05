<?php

namespace Tests\Feature\Api\v2\ArticleEditor;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StorePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testValidation()
    {
        $url = route('api.v2.articles.store');

        $user = factory(User::class)->create();

        $response = $this->postJson($url);
        $response->assertUnauthorized();

        $this->actingAs($user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->create('thumbnail.jpg', 1), $user->id);
        $image = Attachment::createFromFile(UploadedFile::fake()->create('image.jpg', 1), $user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'page',
            'status' => 'publish',
            'title' => 'test title ' . $date,
            'slug' => 'test-slug-' . $date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'sections' => [
                    ['type' => 'text', 'text' => 'text' . $date],
                    ['type' => 'caption', 'caption' => 'caption' . $date],
                    ['type' => 'url', 'url' => 'http://example.com'],
                    ['type' => 'image', 'id' => $image->id],
                ],
            ],
            'categories' => [
                Category::page()->first()->id,
            ],
        ];
        // 投稿形式が空
        $response = $this->postJson($url, ['article' => array_merge($data, ['post_type' => ''])]);
        $response->assertJsonValidationErrors(['article.post_type']);
        // 不正な投稿形式
        $response = $this->postJson($url, ['article' => array_merge($data, ['post_type' => 'test_example'])]);
        $response->assertJsonValidationErrors(['article.post_type']);

        // ステータスが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['status' => ''])]);
        $response->assertJsonValidationErrors(['article.status']);
        // 不正なステータス
        $response = $this->postJson($url, ['article' => array_merge($data, ['status' => 'test_example'])]);
        $response->assertJsonValidationErrors(['article.status']);

        // タイトルが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['title' => ''])]);
        $response->assertJsonValidationErrors(['article.title']);
        // タイトルが256文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['title' => str_repeat('a', 256)])]);
        $response->assertJsonValidationErrors(['article.title']);
        // タイトルが重複
        $other_article = factory(Article::class)->create(['user_id' => factory(User::class)->create()->id]);
        $response = $this->postJson($url, ['article' => array_merge($data, ['title' => $other_article->title])]);
        $response->assertJsonValidationErrors(['article.title']);

        // スラッグが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['slug' => ''])]);
        $response->assertJsonValidationErrors(['article.slug']);
        // スラッグが256文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['slug' => str_repeat('a', 256)])]);
        $response->assertJsonValidationErrors(['article.slug']);
        // スラッグが重複
        $other_article = factory(Article::class)->create(['user_id' => factory(User::class)->create()->id]);
        $response = $this->postJson($url, ['article' => array_merge($data, ['slug' => $other_article->slug])]);
        $response->assertJsonValidationErrors(['article.slug']);

        // 存在しないサムネイルID
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => 99999]])]);
        $response->assertJsonValidationErrors(['article.contents.thumbnail']);
        // 他人の投稿したサムネイルID
        $others_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), factory(User::class)->create()->id);
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => $others_attachment->id]])]);
        $response->assertJsonValidationErrors(['article.contents.thumbnail']);

        // セクションが無い
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => null]])]);
        $response->assertJsonValidationErrors(['article.contents.sections']);
        // セクションが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => []]])]);
        $response->assertJsonValidationErrors(['article.contents.sections']);

        // 本文セクションが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'text', 'text' => '']]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.text']);
        // 本文セクションが2049文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'text', 'text' => str_repeat('a', 2049)]]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.text']);

        // 見出しセクションが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'caption', 'caption' => '']]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.caption']);
        // 見出しセクションが256文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'caption', 'caption' => str_repeat('a', 2049)]]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.caption']);

        // URLセクションが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'url', 'url' => '']]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.url']);
        // URLセクションが不正な形式
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'url', 'url' => 'not_url']]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.url']);

        // 画像セクションが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'image', 'id' => '']]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.id']);
        // 画像セクションが存在しないID
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'image', 'id' => 99999]]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.id']);
        // 画像セクションが他人の投稿したID
        $others_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), factory(User::class)->create()->id);
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['sections' => [['type' => 'image', 'id' => $others_attachment->id]]]])]);
        $response->assertJsonValidationErrors(['article.contents.sections.0.id']);

        // カテゴリが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['categories' => null])]);
        $response->assertJsonValidationErrors(['article.categories']);

        // 存在しないカテゴリ
        $response = $this->postJson($url, ['article' => array_merge($data, ['categories' => [99999]])]);
        $response->assertJsonValidationErrors(['article.categories.0']);

        // 適切なデータ
        $response = $this->postJson($url, ['article' => $data]);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }
}
