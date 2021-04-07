<?php

namespace App\Services\Social;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as OAuthUser;

/**
 * @see https://developer.twitter.com/en/portal/dashboard
 * @see https://syncer.jp/Web/API/Twitter/REST_API/Object/#section-1
 * @see https://developer.twitter.com/en/docs/twitter-api/v1/data-dictionary/object-model/user
 */
class TwitterLoginService extends BaseLoginService
{
    public function validateLoginRestriction(): void
    {
        if (config('services.twitter.login_restriction')) {
            throw new SocialLoginNotAllowedException('ログイン制限');
        }
    }

    protected function validateRegistarRestriction(): void
    {
        if (config('services.twitter.register_restriction')) {
            throw new InvalidSocialUserException('登録制限');
        }
    }

    protected function validateAccessRestriction(OAuthUser $oauthUser): void
    {
        logger('twitter', [$oauthUser]);
        $user = $oauthUser->user;
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
        if ($emailRule && !Str::endsWith($oauthUser->getEmail(), $emailRule)) {
            throw new InvalidSocialUserException('Gmail以外', $oauthUser->getEmail());
        }
    }
}
