<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StoreAddonPostTest extends TestCase
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
        $addon = Attachment::createFromFile(UploadedFile::fake()->create('addon.zip', 1), $user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-post',
            'status' => 'publish',
            'title' => 'test title ' . $date,
            'slug' => 'test-slug-' . $date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'author' => 'test auhtor',
                'file' => $addon->id,
                'description' => 'test description',
                'thanks' => 'tets thanks',
                'license' => 'test license',
            ],
            'tags' => [
                factory(Tag::class)->create()->name,
            ],
            'categories' => [
                Category::pak()->first()->id,
                Category::addon()->first()->id,
                Category::pak128Position()->first()->id,
                Category::license()->first()->id,
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

        // アドオン作者が256文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['author' => str_repeat('a', 256)]])]);
        $response->assertJsonValidationErrors(['article.contents.author']);

        // ファイルIDが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['file' => '']])]);
        $response->assertJsonValidationErrors(['article.contents.file']);
        // 存在しないファイルID
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['file' => 99999]])]);
        $response->assertJsonValidationErrors(['article.contents.file']);
        // 他人の投稿したファイルID
        $others_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), factory(User::class)->create()->id);
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['file' => $others_attachment->id]])]);
        $response->assertJsonValidationErrors(['article.contents.file']);

        // 説明が空
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['description' => '']])]);
        $response->assertJsonValidationErrors(['article.contents.description']);
        // 説明が2049文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['description' => str_repeat('a', 2049)]])]);
        $response->assertJsonValidationErrors(['article.contents.description']);

        // 謝辞が2049文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thanks' => str_repeat('a', 2049)]])]);
        $response->assertJsonValidationErrors(['article.contents.thanks']);

        // ライセンス（その他）が2049文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['license' => str_repeat('a', 2049)]])]);
        $response->assertJsonValidationErrors(['article.contents.license']);

        // タグ名が空
        $response = $this->postJson($url, ['article' => array_merge($data, ['tags' => null])]);
        $response->assertJsonValidationErrors(['article.tags']);
        // 存在しないタグ名
        $response = $this->postJson($url, ['article' => array_merge($data, ['tags' => ['missing_tag']])]);
        $response->assertJsonValidationErrors(['article.tags.0']);
        // タグ名が256文字以上
        $response = $this->postJson($url, ['article' => array_merge($data, ['tags' => [str_repeat('a', 256)]])]);
        $response->assertJsonValidationErrors(['article.tags.0']);

        // カテゴリが空
        $response = $this->postJson($url, ['article' => array_merge($data, ['categories' => null])]);
        $response->assertJsonValidationErrors(['article.categories']);

        // 存在しないカテゴリ
        $response = $this->postJson($url, ['article' => array_merge($data, ['categories' => [99999]])]);
        $response->assertJsonValidationErrors(['article.categories.0']);

        // 適切なデータ
        $response = $this->postJson($url, ['article' => $data]);
        $response->assertStatus(200);
        $get_response = json_decode($this->getJson(route('api.v2.articles.index'))->content(), true);
        $response->assertJson($get_response);
    }
}
