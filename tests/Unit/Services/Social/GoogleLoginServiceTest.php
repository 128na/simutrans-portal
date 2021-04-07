<?php

namespace Tests\Unit\Services\Social;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Social\GoogleLoginService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use SocialiteProviders\Manager\OAuth1\User as OAuth1User;
use Tests\UnitTestCase;

class GoogleLoginServiceTest extends UnitTestCase
{
    private OAuth1User $oauth1User;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oauth1User = new OAuth1User();
        $this->oauth1User->user = [
            'email_verified' => true,
        ];
        $this->oauth1User->email = 'test@gmail.com';
        Config::set('services.google.register_restriction', false);
        Config::set('services.google.login_restriction', false);
        Config::set('services.google.access_restriction.email_verified', true);
    }

    public function test_validateLoginRestriction()
    {
        Config::set('services.google.login_restriction', true);
        $this->expectException(SocialLoginNotAllowedException::class);

        $service = app(GoogleLoginService::class);
        $service->validateLoginRestriction();
    }

    public function test登録済み()
    {
        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(new User());
        });

        $service = app(GoogleLoginService::class);
        $res = $service->findOrRegister($this->oauth1User);
        $this->assertInstanceOf(User::class, $res);
    }

    public function test新規登録()
    {
        Event::fake();
        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(null);
            $mock->shouldReceive('store')->andReturn(new User());
        });

        $service = app(GoogleLoginService::class);
        $res = $service->findOrRegister($this->oauth1User);
        $this->assertInstanceOf(User::class, $res);

        Event::assertDispatched(Registered::class);
    }

    /**
     * @dataProvider dataAccessable
     */
    public function testログイン可能アカウント制限(array $data, ?string $email)
    {
        $this->oauth1User->user = array_merge($this->oauth1User->user, $data);
        $this->oauth1User->email = $email ? $email : $this->oauth1User->email;
        $this->expectException(InvalidSocialUserException::class);

        $service = app(GoogleLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function dataAccessable()
    {
        yield 'メールアドレス未認証' => [['email_verified' => false], null];
    }

    public function testログイン可能アカウント_制限解除()
    {
        Config::set('services.google.register_restriction', false);
        Config::set('services.google.login_restriction', false);
        Config::set('services.google.access_restriction.email_verified', false);

        $this->oauth1User->user = [
            'email_verified' => false,
        ];
        $this->oauth1User->email = 'test@example.jp';

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(new User());
        });

        $service = app(GoogleLoginService::class);
        $res = $service->findOrRegister($this->oauth1User);
        $this->assertInstanceOf(User::class, $res);
    }

    public function test登録制限()
    {
        Config::set('services.google.register_restriction', true);
        $this->expectException(InvalidSocialUserException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(null);
        });

        $service = app(GoogleLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function testログイン制限_管理者()
    {
        $this->expectException(SocialLoginNotAllowedException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(User::make(['role' => 'admin']));
        });

        $service = app(GoogleLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function testログイン制限_削除済みユーザー()
    {
        $this->expectException(SocialLoginNotAllowedException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            User::unguard();
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(User::make(['deleted_at' => now()]));
        });

        $service = app(GoogleLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }
}
