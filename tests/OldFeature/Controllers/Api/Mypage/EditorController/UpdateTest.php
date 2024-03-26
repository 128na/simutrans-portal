<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage\EditorController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use Tests\ArticleTestCase;

class UpdateTest extends ArticleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAddonIntroduction();
    }

    public function test(): void
    {
        $url = '/api/mypage/articles/'.$this->article->id;

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function testログイン済み(): void
    {
        $this->actingAs($this->user);
        $url = '/api/mypage/articles/'.$this->article->id;

        $res = $this->postJson($url);
        $res->assertStatus(422);
    }

    public function testメール未認証(): void
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $url = '/api/mypage/articles/'.$this->article->id;

        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test機能制限(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::ArticleUpdate], ['value' => false]);
        $this->actingAs($this->user);
        $url = '/api/mypage/articles/'.$this->article->id;
        $res = $this->postJson($url);
        $res->assertForbidden();
    }
}
