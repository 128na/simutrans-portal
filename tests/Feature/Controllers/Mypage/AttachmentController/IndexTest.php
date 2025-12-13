<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

use App\Models\Attachment;
use App\Models\User;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
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
        $url = '/mypage/attachments';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にアタッチメント一覧ページを表示(): void
    {
        Attachment::factory()->for($this->user)->count(3)->create();

        $url = '/mypage/attachments';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_アタッチメントがない時も正常に表示(): void
    {
        $url = '/mypage/attachments';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
