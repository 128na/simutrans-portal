<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Closure;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateAddonIntroductionTest extends TestCase
{
    private User $user2;
    private Article $article2;
    private Attachment $file_attachment;
    private Attachment $user2_attachment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAddonIntroduction();
        $this->user2 = User::factory()->create();
        $this->article2 = $this->createAddonIntroduction($this->user2);
        $this->file_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), $this->user->id);
        $this->user2_attachment = Attachment::createFromFile(UploadedFile::fake()->image('other.png', 1), $this->user2->id);
    }

    public function testログイン()
    {
        $url = route('api.v2.articles.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function dataValidation()
    {
        yield 'ステータスが空' => [fn () => ['status' => ''], 'article.status'];
        yield '不正なステータス' => [fn () => ['status' => 'test_example'], 'article.status'];

        yield 'タイトルが空' => [fn () => ['title' => ''], 'article.title'];
        yield 'タイトルが256文字以上' => [fn () => ['title' => str_repeat('a', 256)], 'article.title'];
        yield 'タイトルが重複' => [fn () => ['title' => $this->article2->title], 'article.title'];

        yield 'スラッグが空' => [fn () => ['slug' => ''], 'article.slug'];
        yield 'スラッグが256文字以上' => [fn () => ['slug' => str_repeat('a', 256)], 'article.slug'];
        yield 'スラッグが重複' => [fn () => ['slug' => $this->article2->slug], 'article.slug'];

        yield '存在しないサムネイルID' => [fn () => ['contents' => ['thumbnail' => 99999]], 'article.contents.thumbnail'];
        yield '画像以外' => [fn () => ['contents' => ['thumbnail' => $this->file_attachment->id]], 'article.contents.thumbnail'];
        yield '他人の投稿したサムネイルID' => [fn () => ['contents' => ['thumbnail' => $this->user2_attachment->id]], 'article.contents.thumbnail'];
        yield 'アドオン作者が空' => [fn () => ['contents' => ['author' => '']], 'article.contents.author'];
        yield 'アドオン作者が256文字以上' => [fn () => ['contents' => ['author' => str_repeat('a', 256)]], 'article.contents.author'];
        yield 'リンクが空' => [fn () => ['contents' => ['link' => '']], 'article.contents.link'];
        yield 'リンクが不正なURL' => [fn () => ['contents' => ['link' => 'not_url']], 'article.contents.link'];
        yield '説明が空' => [fn () => ['contents' => ['description' => '']], 'article.contents.description'];
        yield '説明が2049文字以上' => [fn () => ['contents' => ['description' => str_repeat('a', 2049)]], 'article.contents.description'];
        yield '謝辞が2049文字以上' => [fn () => ['contents' => ['thanks' => str_repeat('a', 2049)]], 'article.contents.thanks'];
        yield 'ライセンス（その他）が2049文字以上' => [fn () => ['contents' => ['license' => str_repeat('a', 2049)]], 'article.contents.license'];
        yield 'タグ名が空' => [fn () => ['tags' => null], 'article.tags'];
        yield '存在しないタグ名' => [fn () => ['tags' => ['missing_tag']], 'article.tags.0'];
        yield 'タグ名が256文字以上' => [fn () => ['tags' => [str_repeat('a', 256)]], 'article.tags.0'];
        yield 'カテゴリが空' => [fn () => ['categories' => null], 'article.categories'];
        yield '存在しないカテゴリ' => [fn () => ['categories' => [99999]], 'article.categories.0'];
        yield 'OK' => [fn () => [], null];
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        $url = route('api.v2.articles.update', $this->article);
        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
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
                Tag::factory()->create()->name,
            ],
            'categories' => [
                Category::pak()->first()->id,
                Category::addon()->first()->id,
                Category::pak128Position()->first()->id,
                Category::license()->first()->id,
            ],
        ];

        $data = array_merge($data, Closure::bind($fn, $this)());

        $res = $this->postJson($url, ['article' => $data]);
        if (is_null($error_field)) {
            $res->assertStatus(200);
            $get_response = json_decode($this->getJson(route('api.v2.articles.index'))->content(), true);
            $res->assertJson($get_response);
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function test他人の投稿()
    {
        $this->actingAs($this->user);

        $url = route('api.v2.articles.update', $this->article2);

        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function testPreview()
    {
        $url = route('api.v2.articles.update', $this->article);
        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
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
                Tag::factory()->create()->name,
            ],
            'categories' => [
                Category::pak()->first()->id,
                Category::addon()->first()->id,
                Category::pak128Position()->first()->id,
                Category::license()->first()->id,
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
