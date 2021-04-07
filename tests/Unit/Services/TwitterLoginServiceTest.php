<?php

namespace Tests\Unit\Services;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TwitterLoginService;
use Config;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use SocialiteProviders\Manager\OAuth1\User as OAuth1User;
use Tests\UnitTestCase;

class TwitterLoginServiceTest extends UnitTestCase
{
    private OAuth1User $oauth1User;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oauth1User = new OAuth1User();
        $this->oauth1User->email = 'test@example.com';
        $this->oauth1User->user = [
            'needs_phone_verification' => false,
            'suspended' => false,
            'default_profile_image' => false,
            'default_profile' => false,
            'statuses_count' => 1001,
            'followers_count' => 1001,
            'created_at' => now()->modify('-1001 days')->format('D M d H:i:s O Y'),
        ];
        Config::set('services.twitter.register_restriction', false);
        Config::set('services.twitter.login_restriction', false);
        Config::set('services.twitter.access_restriction.needs_phone_verification', true);
        Config::set('services.twitter.access_restriction.suspended', true);
        Config::set('services.twitter.access_restriction.default_profile_image', true);
        Config::set('services.twitter.access_restriction.default_profile', true);
        Config::set('services.twitter.access_restriction.statuses_count', 1000);
        Config::set('services.twitter.access_restriction.followers_count', 1000);
        Config::set('services.twitter.access_restriction.created_at', 1000);
        Config::set('services.twitter.access_restriction.email_suffix', '.com');
    }

    public function test登録済み()
    {
        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(new User());
        });

        $service = app(TwitterLoginService::class);
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

        $service = app(TwitterLoginService::class);
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

        $service = app(TwitterLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function dataAccessable()
    {
        yield '電話番号未認証' => [['needs_phone_verification' => true], null];
        yield '凍結' => [['suspended' => true], null];
        yield 'デフォルトアイコン' => [['default_profile_image' => true], null];
        yield 'デフォルトプロフィール' => [['default_profile' => true], null];
        yield 'ツイートの数が少ない' => [['statuses_count' => 1000], null];
        yield 'フォロワーの数が少ない' => [['followers_count' => 1000], null];
        yield '登録したばかり' => [['created_at' => now()->modify('-1000 days')->format('D M d H:i:s O Y')], null];
        yield 'メールドメイン制限' => [[], 'test@example.jp'];
    }

    public function testログイン可能アカウント_制限解除()
    {
        Config::set('services.twitter.register_restriction', false);
        Config::set('services.twitter.login_restriction', false);
        Config::set('services.twitter.access_restriction.needs_phone_verification', false);
        Config::set('services.twitter.access_restriction.suspended', false);
        Config::set('services.twitter.access_restriction.default_profile_image', false);
        Config::set('services.twitter.access_restriction.default_profile', false);
        Config::set('services.twitter.access_restriction.statuses_count', -1);
        Config::set('services.twitter.access_restriction.followers_count', -1);
        Config::set('services.twitter.access_restriction.created_at', 0);
        Config::set('services.twitter.access_restriction.email_suffix', '');

        $this->oauth1User->user = [
            'needs_phone_verification' => true,
            'suspended' => true,
            'default_profile_image' => true,
            'default_profile' => true,
            'statuses_count' => 0,
            'followers_count' => 0,
            'created_at' => now()->format('D M d H:i:s O Y'),
        ];
        $this->oauth1User->email = 'test@example.jp';

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(new User());
        });

        $service = app(TwitterLoginService::class);
        $res = $service->findOrRegister($this->oauth1User);
        $this->assertInstanceOf(User::class, $res);
    }

    public function test登録制限()
    {
        Config::set('services.twitter.register_restriction', true);
        $this->expectException(InvalidSocialUserException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(null);
        });

        $service = app(TwitterLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function testログイン制限()
    {
        Config::set('services.twitter.login_restriction', true);
        $this->expectException(SocialLoginNotAllowedException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(new User());
        });

        $service = app(TwitterLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function testログイン制限_管理者()
    {
        $this->expectException(SocialLoginNotAllowedException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(User::make(['role' => 'admin']));
        });

        $service = app(TwitterLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }

    public function testログイン制限_削除済みユーザー()
    {
        $this->expectException(SocialLoginNotAllowedException::class);

        $this->mock(UserRepository::class, function (MockInterface $mock) {
            User::unguard();
            $mock->shouldReceive('findByEmailWithTrashed')->andReturn(User::make(['deleted_at' => now()]));
        });

        $service = app(TwitterLoginService::class);
        $service->findOrRegister($this->oauth1User);
    }
}
