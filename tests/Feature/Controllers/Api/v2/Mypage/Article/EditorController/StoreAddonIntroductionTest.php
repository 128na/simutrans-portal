<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\Article\EditorController;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\ArticleTestCase;

class StoreAddonIntroductionTest extends ArticleTestCase
{
    public function testログイン()
    {
        $url = route('api.v2.articles.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    /**
     * @dataProvider dataStoreArticleValidation
     * @dataProvider dataArticleValidation
     * @dataProvider dataAddonValidation
     * @dataProvider dataAddonIntroductionValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        Bus::fake();
        $url = route('api.v2.articles.store');

        $this->actingAs($this->user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);

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
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            $res->assertJsonValidationErrors($error_field);
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }

    public function testPreview()
    {
        $url = route('api.v2.articles.store');

        $user = User::factory()->create();
        $this->actingAs($user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);

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
