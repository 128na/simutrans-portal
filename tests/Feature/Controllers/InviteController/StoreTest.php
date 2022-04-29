<?php

namespace Tests\Feature\Controllers\InviteController;

use App\Notifications\UserInvited;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private array $data = [
        'name' => 'example',
        'email' => 'example@example.com',
        'password' => 'example123456',
        'password_confirmation' => 'example123456',
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

        $response = $this->post(
            route('invite.index', ['invitation_code' => $this->user->invitation_code]),
            $this->data
        );

        $response->assertRedirect(route('mypage.index'));
        $this->assertAuthenticated();
        Event::assertDispatched(Registered::class);
        Notification::assertSentTo(
            [$this->user], UserInvited::class
        );
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, string $key)
    {
        $response = $this->post(
            route('invite.index', ['invitation_code' => $this->user->invitation_code]),
            array_merge($this->data, $data)
        );

        $response->assertSessionHasErrors($key);
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
        yield 'password_confirmationが不一致' => [
            ['password_confirmation' => '123'], 'password',
        ];
    }
}