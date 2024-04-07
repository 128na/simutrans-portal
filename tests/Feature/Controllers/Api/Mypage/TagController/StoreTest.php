<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\TagController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class StoreTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/tags';

        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function test_機能制限(): void
    {
        $url = '/api/mypage/tags';

        ControllOption::updateOrCreate(['key' => ControllOptionKey::TagUpdate], ['value' => false]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function test(): void
    {
        $url = '/api/mypage/tags';

        $this->actingAs($this->user);
        $res = $this->postJson($url, ['name' => 'example']);
        $res->assertCreated();
        $this->assertTrue(Tag::where('name', 'example')->exists());
    }
}
