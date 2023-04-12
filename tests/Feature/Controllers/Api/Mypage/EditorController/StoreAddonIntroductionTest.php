<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use App\Models\Tag;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Tests\ArticleTestCase;

final class StoreAddonIntroductionTest extends ArticleTestCase
{
    #[DataProvider('dataStoreArticleValidation')]
    #[DataProvider('dataArticleValidation')]
    #[DataProvider('dataAddonValidation')]
    #[DataProvider('dataAddonIntroductionValidation')]
    public function testValidation(Closure $fn, ?string $error_field): void
    {
        Bus::fake();
        $url = '/api/mypage/articles';

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
