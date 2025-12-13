<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\TagController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    private Tag $tag;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tag = Tag::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/tags/'.$this->tag->id;

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_機能制限(): void
    {
        $url = '/api/v2/tags/'.$this->tag->id;

        ControllOption::updateOrCreate(['key' => ControllOptionKey::TagUpdate], ['value' => false]);
        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertForbidden();
    }

    public function test(): void
    {
        $url = '/api/v2/tags/'.$this->tag->id;

        $this->actingAs($this->user);
        $testResponse = $this->postJson($url, ['description' => 'dummy']);
        $testResponse->assertOk();
        $this->assertSame('dummy', Tag::find($this->tag->id)->description);
    }

    public function test編集ロック(): void
    {
        $this->tag->update(['editable' => false]);
        $url = '/api/v2/tags/'.$this->tag->id;

        $this->actingAs($this->user);
        $data = ['description' => 'dummy'];
        $testResponse = $this->postJson($url, $data);
        $testResponse->assertForbidden();
    }
}
