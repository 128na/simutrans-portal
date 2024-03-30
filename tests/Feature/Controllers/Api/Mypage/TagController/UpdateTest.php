<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\TagController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tag = Tag::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/tags/'.$this->tag->id;

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function test_メール未認証(): void
    {
        $url = '/api/mypage/tags/'.$this->tag->id;

        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test_機能制限(): void
    {
        $url = '/api/mypage/tags/'.$this->tag->id;

        ControllOption::updateOrCreate(['key' => ControllOptionKey::TagUpdate], ['value' => false]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test(): void
    {
        $url = '/api/mypage/tags/'.$this->tag->id;

        $this->actingAs($this->user);
        $res = $this->postJson($url, ['description' => 'dummy']);
        $res->assertOk();
        $this->assertSame('dummy', Tag::find($this->tag->id)->description);
    }

    public function test編集ロック(): void
    {
        $this->tag->update(['editable' => false]);
        $url = '/api/mypage/tags/'.$this->tag->id;

        $this->actingAs($this->user);
        $data = ['description' => 'dummy'];
        $res = $this->postJson($url, $data);
        $res->assertForbidden();
    }
}
