<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\Article\EditorController;

use App\Constants\ControllOptionKeys;
use App\Models\ControllOption;
use Tests\ArticleTestCase;

class UpdateTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAddonIntroduction();
    }

    public function test()
    {
        $url = route('api.v2.articles.update', $this->article);

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function testログイン済み()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.articles.update', $this->article);

        $res = $this->postJson($url);
        $res->assertStatus(422);
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $url = route('api.v2.articles.update', $this->article);

        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test機能制限()
    {
        ControllOption::create(['key' => ControllOptionKeys::ARTICLE_UPDATE, 'value' => false]);
        $this->actingAs($this->user);
        $url = route('api.v2.articles.update', $this->article);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }
}
