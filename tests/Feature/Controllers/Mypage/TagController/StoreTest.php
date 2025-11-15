<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\TagController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class StoreTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/tags';

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_機能制限(): void
    {
        $url = '/api/v2/tags';

        ControllOption::updateOrCreate(['key' => ControllOptionKey::TagUpdate], ['value' => false]);
        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertForbidden();
    }

    public function test(): void
    {
        $url = '/api/v2/tags';

        $this->actingAs($this->user);
        $testResponse = $this->postJson($url, ['name' => 'example']);
        $testResponse->assertCreated();
        $this->assertTrue(Tag::where('name', 'example')->exists());
    }
}
