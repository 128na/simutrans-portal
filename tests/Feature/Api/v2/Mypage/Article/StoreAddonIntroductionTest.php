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

class StoreAddonIntroductionTest extends TestCase
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

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'title' => 'test title ' . $date,
            'slug' => 'test-slug-' . $date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'author' => 'test auhtor',
                'link' => 'http://example.com',
                'description' => 'test description',
                'thanks' => 'tets thanks',
                'license' => 'test license',
                'agreement' => true,
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
        $res = $this->postJson($url, ['article' => array_merge($data, ['post_type' => ''])]);
        $res->assertJsonValidationErrors(['article.post_type']);
        // 不正な投稿形式
        $res = $this->postJson($url, ['article' => array_merge($data, ['post_type' => 'test_example'])]);
        $res->assertJsonValidationErrors(['article.post_type']);

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
        $other_article = factory(Article::class)->create(['user_id' => factory(User::class)->create()->id]);
        $res = $this->postJson($url, ['article' => array_merge($data, ['title' => $other_article->title])]);
        $res->assertJsonValidationErrors(['article.title']);

        // スラッグが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['slug' => ''])]);
        $res->assertJsonValidationErrors(['article.slug']);
        // スラッグが256文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['slug' => str_repeat('a', 256)])]);
        $res->assertJsonValidationErrors(['article.slug']);
        // スラッグが重複
        $other_article = factory(Article::class)->create(['user_id' => factory(User::class)->create()->id]);
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
        $others_attachment = Attachment::createFromFile(UploadedFile::fake()->image('other.png', 1), factory(User::class)->create()->id);
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thumbnail' => $others_attachment->id]])]);
        $res->assertJsonValidationErrors(['article.contents.thumbnail']);

        // アドオン作者が空
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['author' => '']])]);
        $res->assertJsonValidationErrors(['article.contents.author']);
        // アドオン作者が256文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['author' => str_repeat('a', 256)]])]);
        $res->assertJsonValidationErrors(['article.contents.author']);

        // リンクが空
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['link' => '']])]);
        $res->assertJsonValidationErrors(['article.contents.link']);
        // リンクが不正なURL
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['link' => 'not_url']])]);
        $res->assertJsonValidationErrors(['article.contents.link']);

        // 説明が空
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['description' => '']])]);
        $res->assertJsonValidationErrors(['article.contents.description']);
        // 説明が2049文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['description' => str_repeat('a', 2049)]])]);
        $res->assertJsonValidationErrors(['article.contents.description']);

        // 謝辞が2049文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['thanks' => str_repeat('a', 2049)]])]);
        $res->assertJsonValidationErrors(['article.contents.thanks']);

        // ライセンス（その他）が2049文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['contents' => ['license' => str_repeat('a', 2049)]])]);
        $res->assertJsonValidationErrors(['article.contents.license']);

        // タグ名が空
        $res = $this->postJson($url, ['article' => array_merge($data, ['tags' => null])]);
        $res->assertJsonValidationErrors(['article.tags']);
        // 存在しないタグ名
        $res = $this->postJson($url, ['article' => array_merge($data, ['tags' => ['missing_tag']])]);
        $res->assertJsonValidationErrors(['article.tags.0']);
        // タグ名が256文字以上
        $res = $this->postJson($url, ['article' => array_merge($data, ['tags' => [str_repeat('a', 256)]])]);
        $res->assertJsonValidationErrors(['article.tags.0']);

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
}
