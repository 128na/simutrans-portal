<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage\EditorController;

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
    public function testValidation(Closure $fn, ?string $errorField): void
    {
        Bus::fake();
        $url = '/api/mypage/articles';
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
                ['id' => Category::page()->first()->id],
            ],
            'articles' => [],
        ];

        $data = array_merge($data, Closure::bind($fn, $this)());

        $res = $this->postJson($url, ['article' => $data]);
        if (is_null($errorField)) {
            $res->assertStatus(200);
            $get_response = json_decode($this->getJson('/api/mypage/articles')->content(), true);
            $res->assertJson($get_response);
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            $res->assertJsonValidationErrors($errorField);
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }
}
