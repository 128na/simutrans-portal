<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Constants\ControllOptionKeys;
use App\Models\ControllOption;
use Tests\ArticleTestCase;

class StoreTest extends ArticleTestCase
{
    public function test(): void
    {
        $url = '/api/mypage/articles';

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function testログイン済み(): void
    {
        $this->actingAs($this->user);
        $url = '/api/mypage/articles';

        $res = $this->postJson($url);
        $res->assertStatus(422);
    }

    public function testメール未認証(): void
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $url = '/api/mypage/articles';

        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test機能制限(): void
    {
        ControllOption::create(['key' => ControllOptionKeys::ARTICLE_UPDATE, 'value' => false]);
        $this->actingAs($this->user);
        $url = '/api/mypage/articles';
        $res = $this->postJson($url);
        $res->assertForbidden();
    }
}
