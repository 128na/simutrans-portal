<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\ArticleTestCase;

class StorePageTest extends ArticleTestCase
{
    /**
     * @dataProvider dataStoreArticleValidation
     * @dataProvider dataArticleValidation
     * @dataProvider dataPageValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        Bus::fake();
        $url = route('api.v2.articles.store');
        $this->actingAs($this->user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $image = $this->createFromFile(UploadedFile::fake()->image('image.jpg', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'page',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'sections' => [
                    ['type' => 'text', 'text' => 'text'.$date],
                    ['type' => 'caption', 'caption' => 'caption'.$date],
                    ['type' => 'url', 'url' => 'http://example.com'],
                    ['type' => 'image', 'id' => $image->id],
                ],
            ],
            'categories' => [
                Category::page()->first()->id,
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

        $this->actingAs($this->user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $image = $this->createFromFile(UploadedFile::fake()->image('image.jpg', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'page',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'sections' => [
                    ['type' => 'text', 'text' => 'text'.$date],
                    ['type' => 'caption', 'caption' => 'caption'.$date],
                    ['type' => 'url', 'url' => 'http://example.com'],
                    ['type' => 'image', 'id' => $image->id],
                ],
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
