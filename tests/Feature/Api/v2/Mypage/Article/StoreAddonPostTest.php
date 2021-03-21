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

class StoreAddonPostTest extends TestCase
{
    private User $user2;
    private Article $article2;
    private Attachment $file_attachment;
    private Attachment $user2_attachment;
    private Attachment $user2_file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create();
        $this->article2 = Article::factory()->create(['user_id' => $this->user2->id]);
        $this->file_attachment = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), $this->user->id);
        $this->user2_attachment = Attachment::createFromFile(UploadedFile::fake()->image('other.png', 1), $this->user2->id);
        $this->user2_file = Attachment::createFromFile(UploadedFile::fake()->image('other.zip', 1), $this->user2->id);
    }

    public function dataValidation()
    {
        yield '投稿形式が空' => [fn () => ['post_type' => ''], 'article.post_type'];
        yield '不正な投稿形式' => [fn () => ['post_type' => 'test_example'], 'article.post_type'];

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
        yield 'アドオン作者が256文字以上' => [fn () => ['contents' => ['author' => str_repeat('a', 256)]], 'article.contents.author'];

        yield 'ファイルIDが空' => [fn () => ['contents' => ['file' => '']], 'article.contents.file'];
        yield '存在しないファイルID' => [fn () => ['contents' => ['file' => 99999]], 'article.contents.file'];
        yield '他人の投稿したファイルID' => [fn () => ['contents' => ['file' => $this->user2_file->id]], 'article.contents.file'];

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
        $url = route('api.v2.articles.store');
        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $addon = Attachment::createFromFile(UploadedFile::fake()->create('addon.zip', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-post',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'author' => 'test auhtor',
                'file' => $addon->id,
                'description' => 'test description',
                'thanks' => 'tets thanks',
                'license' => 'test license',
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

    public function testPreview()
    {
        $url = route('api.v2.articles.store');

        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $addon = Attachment::createFromFile(UploadedFile::fake()->create('addon.zip', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'addon-post',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'author' => 'test auhtor',
                'file' => $addon->id,
                'description' => 'test description',
                'thanks' => 'tets thanks',
                'license' => 'test license',
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
