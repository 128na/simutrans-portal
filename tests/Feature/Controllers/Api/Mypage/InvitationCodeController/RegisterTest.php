<?php

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Constants\ControllOptionKeys;
use App\Models\ControllOption;
use App\Notifications\UserInvited;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    private array $data = [
        'name' => 'example',
        'email' => 'example@example.com',
        'password' => 'example123456',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user->update(['email' => 'invite@example.com', 'invitation_code' => Str::uuid()]);
    }

    public function test()
    {
        Notification::fake();
        Event::fake();
        $this->assertGuest();

        $response = $this->postJson(
            "/api/mypage/invite/{$this->user->invitation_code}",
            $this->data
        );

        $response->assertCreated();
        $this->assertAuthenticated();
        Event::assertDispatched(Registered::class);
        Notification::assertSentTo(
            [$this->user], UserInvited::class
        );
    }

    public function test機能無効()
    {
        ControllOption::create(['key' => ControllOptionKeys::INVITATION_CODE, 'value' => false]);

        $response = $this->postJson(
            "/api/mypage/invite/{$this->user->invitation_code}",
            $this->data
        );
        $response->assertStatus(403);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, string $key)
    {
        $response = $this->postJson(
            "/api/mypage/invite/{$this->user->invitation_code}",
            array_merge($this->data, $data)
        );
        $response->assertJsonValidationErrorFor($key);
    }

    public function dataValidation()
    {
        yield 'nameが空' => [
            ['name' => ''], 'name',
        ];
        yield 'nameが101文字以上' => [
            ['name' => str_repeat('a', 101)], 'name',
        ];
        yield 'emailが空' => [
            ['email' => ''], 'email',
        ];
        yield 'emailが不正' => [
            ['email' => 'a'], 'email',
        ];
        yield 'emailが重複' => [
            ['email' => 'invite@example.com'], 'email',
        ];
        yield 'passwordが空' => [
            ['password' => ''], 'password',
        ];
        yield 'passwordが10文字以下' => [
            ['password' => str_repeat('a', 10)], 'password',
        ];
    }
}