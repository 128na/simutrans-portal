<?php

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use App\Models\Tag;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\ArticleTestCase;

class UpdateAddonPostTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAddonPost();
        $this->article2 = $this->createAddonPost($this->user2);
    }

    /**
     * @dataProvider dataArticleValidation
     * @dataProvider dataAddonValidation
     * @dataProvider dataAddonPostValidation
     */
    public function testValidation(Closure $fn, ?string $error_field)
    {
        Bus::fake();
        $url = "/api/mypage/articles/{$this->article->id}";
        $this->actingAs($this->user);

        $thumbnail = $this->createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $this->user->id);
        $addon = $this->createFromFile(UploadedFile::fake()->create('addon.zip', 1), $this->user->id);

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
                ['id' => Tag::factory()->create()->id],
            ],
            'categories' => [
                ['id' => Category::pak()->first()->id],
                ['id' => Category::addon()->first()->id],
                ['id' => Category::pak128Position()->first()->id],
                ['id' => Category::license()->first()->id],
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
