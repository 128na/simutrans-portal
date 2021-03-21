<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Attachment;
use App\Models\Category;
use Closure;
use Illuminate\Http\UploadedFile;
use Tests\ArticleTestCase;

class UpdatePageTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createPage();
        $this->article2 = $this->createPage($this->user2);
    }

    /**
     * @dataProvider dataArticleValidation
     * @dataProvider dataPageValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        $url = route('api.v2.articles.update', $this->article);
        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $image = Attachment::createFromFile(UploadedFile::fake()->image('image.jpg', 1), $this->user->id);

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
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function testPreview()
    {
        $url = route('api.v2.articles.update', $this->article);
        $this->actingAs($this->user);

        $thumbnail = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $image = Attachment::createFromFile(UploadedFile::fake()->image('image.jpg', 1), $this->user->id);

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
