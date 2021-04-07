<?php

namespace App\Services\Social;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use Laravel\Socialite\Contracts\User as OAuthUser;

/**
 * @see https://console.cloud.google.com/apis/dashboard
 * @see https://qiita.com/masaha03/items/927abc3c6b93911fb703
 */
class GoogleLoginService extends BaseLoginService
{
    public function validateLoginRestriction(): void
    {
        if (config('services.google.login_restriction')) {
            throw new SocialLoginNotAllowedException('ログイン制限');
        }
    }

    protected function validateRegistarRestriction(): void
    {
        if (config('services.google.register_restriction')) {
            throw new InvalidSocialUserException('登録制限');
        }
    }

    protected function validateAccessRestriction(OAuthUser $oauthUser): void
    {
        logger('google', [$oauthUser]);
        $user = $oauthUser->user;
        if (config('services.google.access_restriction.email_verified') && $user['email_verified'] !== true) {
            throw new InvalidSocialUserException('電話番号未認証');
        }
    }
}
