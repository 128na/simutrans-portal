<?php

namespace App\Services;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use SocialiteProviders\Manager\OAuth1\User as OAuth1User;

class TwitterLoginService extends Service
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * 指定アカウント情報状態が妥当か.
     *
     * @see https://syncer.jp/Web/API/Twitter/REST_API/Object/#section-1
     * @see https://developer.twitter.com/en/docs/twitter-api/v1/data-dictionary/object-model/user
     */
    private function validateAccessable(OAuth1User $oauth1User): void
    {
        logger('twitter', [$oauth1User]);
        $user = $oauth1User->user;
        if (config('services.twitter.access_restriction.needs_phone_verification') && $user['needs_phone_verification']) {
            throw new InvalidSocialUserException('電話番号未認証');
        }
        if (config('services.twitter.access_restriction.suspended') && $user['suspended']) {
            throw new InvalidSocialUserException('凍結');
        }
        if (config('services.twitter.access_restriction.default_profile_image') && $user['default_profile_image']) {
            throw new InvalidSocialUserException('デフォルトアイコン');
        }
        if (config('services.twitter.access_restriction.default_profile') && $user['default_profile']) {
            throw new InvalidSocialUserException('デフォルトプロフィール設定');
        }
        if ($user['statuses_count'] <= config('services.twitter.access_restriction.statuses_count')) {
            throw new InvalidSocialUserException('ツイートの数が少ない', $user['statuses_count']);
        }
        if ($user['followers_count'] <= config('services.twitter.access_restriction.followers_count')) {
            throw new InvalidSocialUserException('フォロワーの数が少ない', $user['followers_count']);
        }
        $createdAt = CarbonImmutable::createFromFormat('D M d H:i:s O Y', $user['created_at']);
        if ($createdAt->diffInDays(today()) < config('services.twitter.access_restriction.created_at')) {
            throw new InvalidSocialUserException('登録したばかり', $createdAt->toDateTimeString());
        }

        $emailRule = config('services.twitter.access_restriction.email_suffix');
        if ($emailRule && !Str::endsWith($oauth1User->getEmail(), $emailRule)) {
            throw new InvalidSocialUserException('Gmail以外', $oauth1User->getEmail());
        }
    }

    /**
     * Twitter連携からの新規登録が可能か.
     */
    private function validateRegistarable(): void
    {
        if (config('services.twitter.register_restriction')) {
            throw new InvalidSocialUserException('登録制限');
        }
    }

    /**
     * Twitter連携からのログインが可能か.
     */
    private function validateLoginable(User $user): void
    {
        if (config('services.twitter.login_restriction')) {
            throw new SocialLoginNotAllowedException('ログイン制限', $user->email);
        }
        if ($user->trashed()) {
            throw new SocialLoginNotAllowedException('削除済みユーザー', $user->email);
        }
        if ($user->isAdmin()) {
            throw new SocialLoginNotAllowedException('管理者ユーザー', $user->email);
        }
    }

    /**
     * 新規登録またはログイン.
     */
    public function findOrRegister(OAuth1User $oauth1User): User
    {
        $this->validateAccessable($oauth1User);

        $user = $this->userRepository->findByEmailWithTrashed($oauth1User->getEmail());

        // ユーザー未作成のときは新規登録
        if (is_null($user)) {
            $this->validateRegistarable();
            $user = $this->register($oauth1User);
        }
        $this->validateLoginable($user);

        return $user;
    }

    /**
     * ユーザー登録実行.
     */
    private function register(OAuth1User $oauth1User): User
    {
        $user = $this->userRepository->store([
            'name' => $oauth1User->getName(),
            'email' => $oauth1User->getEmail(),
            'role' => config('role.user'),
        ]);

        event(new Registered($user));

        return $user;
    }
}
