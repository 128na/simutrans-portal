<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\UserController;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
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

    public function testStore(): void
    {
        Notification::fake();

        $data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'nickname' => $this->user->nickname,
            'profile' => [
                'data' => [
                    'avatar' => $this->user->profile->data->avatar,
                    'description' => $this->user->profile->data->description,
                ],
            ],
        ];

        $url = '/api/mypage/user';

        $this->actingAs($this->user);

        $testResponse = $this->postJson($url, ['user' => $data]);
        $testResponse->assertOK();

        Notification::assertNothingSent();
    }

    public function test_メールアドレス変更時は確認メール再送する(): void
    {
        Notification::fake();

        $data = [
            'name' => $this->user->name,
            'email' => 'new@example.com',
            'profile' => [
                'data' => [
                    'avatar' => $this->user->profile->data->avatar,
                    'description' => $this->user->profile->data->description,
                    'website' => $this->user->profile->data->website,
                ],
            ],
        ];
        $this->actingAs($this->user);

        $url = '/api/mypage/user';
        $testResponse = $this->postJson($url, ['user' => $data]);
        $testResponse->assertOk();

        $this->assertEquals($this->user->fresh()->email, 'new@example.com');

        Notification::assertSentTo($this->user, VerifyEmail::class);
    }
}
