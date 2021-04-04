<?php

namespace Tests\Feature\Controllers\Api\v2\Admin;

use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Support\Facades\Bus;
use Tests\AdminTestCase;

class ArticleControllerTest extends AdminTestCase
{
    /**
     * @dataProvider dataUsers
     */
    public function test_記事一覧_権限チェック(?string $prop, int $expected_status)
    {
        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $url = route('api.v2.admin.articles.index');

        $response = $this->getJson($url);
        $response->assertStatus($expected_status);
    }

    /**
     * @dataProvider dataUsers
     */
    public function test_記事更新_権限チェック(?string $prop, int $expected_status)
    {
        Bus::fake();
        $url = route('api.v2.admin.articles.update', $this->article);
        $data = ['article' => ['status' => 'private']];

        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $response = $this->putJson($url, $data);
        $response->assertStatus($expected_status);

        if ($expected_status === 200) {
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }

    public function test_記事更新_バリデーション項目以外更新されないこと()
    {
        $url = route('api.v2.admin.articles.update', $this->article);
        $data = ['article' => ['status' => 'private', 'title' => 'update_'.$this->article->title]];

        $this->actingAs($this->admin);
        $response = $this->putJson($url, $data);
        $response->assertStatus(200);
        $this->assertEquals('private', $this->article->fresh()->status);
        $this->assertEquals($this->article->title, $this->article->fresh()->title);
    }

    /**
     * @dataProvider dataUsers
     */
    public function test_記事削除_権限チェック(?string $prop, int $expected_status)
    {
        Bus::fake();
        $this->assertNull($this->article->deleted_at);
        $url = route('api.v2.admin.articles.destroy', $this->article);

        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $response = $this->deleteJson($url);
        $response->assertStatus($expected_status);

        if ($expected_status === 200) {
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }

    public function test_論理削除チェック()
    {
        $this->actingAs($this->admin);

        $url = route('api.v2.admin.articles.destroy', $this->article);
        $response = $this->deleteJson($url);

        $this->assertNotNull($this->article->fresh()->deleted_at);
    }
}
