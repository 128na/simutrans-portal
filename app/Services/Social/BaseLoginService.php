<?php

namespace App\Services\Social;

use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Service;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Contracts\User as OAuthUser;

abstract class BaseLoginService extends Service
{
    protected UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * 指定ドライバでのログインが有効か.
     *
     * @throws SocialLoginNotAllowedException
     */
    abstract public function validateLoginRestriction(): void;

    /**
     * 指定アカウント情報状態が妥当か.
     *
     * @throws InvalidSocialUserException
     */
    abstract protected function validateAccessRestriction(OAuthUser $oauthUser): void;

    /**
     * 連携からの新規登録が可能か.
     *
     * @throws InvalidSocialUserException
     */
    abstract protected function validateRegistarRestriction(): void;

    /**
     * 連携ログインが可能か.
     *
     * @throws SocialLoginNotAllowedException
     */
    protected function validateLoginable(User $user): void
    {
        if ($user->trashed()) {
            throw new SocialLoginNotAllowedException('削除済みユーザー', $user->email);
        }
        if ($user->isAdmin()) {
            throw new SocialLoginNotAllowedException('管理者ユーザー', $user->email);
        }
    }

    /**
     * 新規登録またはログイン可能なユーザーを取得.
     *
     * @throws SocialLoginNotAllowedException|InvalidSocialUserException
     */
    public function findOrRegister(OAuthUser $oauthUser): User
    {
        $this->validateAccessRestriction($oauthUser);

        $user = $this->userRepository->findByEmailWithTrashed($oauthUser->getEmail());

        // ユーザー未作成のときは新規登録
        if (is_null($user)) {
            $this->validateRegistarRestriction();
            $user = $this->register($oauthUser);
        }
        $this->validateLoginable($user);

        return $user;
    }

    /**
     * ユーザー登録実行.
     */
    protected function register(OAuthUser $oauthUser): User
    {
        $user = $this->userRepository->store([
            'name' => $oauthUser->getName(),
            'email' => $oauthUser->getEmail(),
            'role' => config('role.user'),
        ]);

        event(new Registered($user));

        return $user;
    }
}
