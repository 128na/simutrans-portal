<?php

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\ArticleTestCase;

class UpdateMarkdownTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createMarkdown();
        $this->article2 = $this->createMarkdown($this->user2);
    }

    /**
     * @dataProvider dataArticleValidation
     * @dataProvider dataMarkdownValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        Bus::fake();
        $url = "/api/mypage/articles/{$this->article->id}";

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);

        $date = now()->format('YmdHis');
        $data = [
            'post_type' => 'markdown',
            'status' => 'publish',
            'title' => 'test title '.$date,
            'slug' => 'test-slug-'.$date,
            'contents' => [
                'thumbnail' => $thumbnail->id,
                'markdown' => '# hello',
            ],
            'categories' => [
                ['id' => Category::page()->first()->id],
            ],
        ];

        $data = array_merge($data, Closure::bind($fn, $this)());

        $res = $this->postJson($url, ['article' => $data]);
        if (is_null($error_field)) {
            $res->assertStatus(200);
            $get_response = json_decode($this->getJson('/api/mypage/articles')->content(), true);
            $res->assertJson($get_response);
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            $res->assertJsonValidationErrors($error_field);
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }
}
