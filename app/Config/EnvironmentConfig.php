<?php

declare(strict_types=1);

namespace App\Config;

/**
 * 型安全な環境変数アクセスを提供するReadonlyクラス
 *
 * 環境変数を型安全にアクセスでき、IDEの補完が効くようになります。
 * 必須の環境変数とオプションの環境変数を明示的に区別します。
 */
final readonly class EnvironmentConfig
{
    public function __construct(
        // Application (必須)
        public string $appName,
        public string $appEnv,
        public bool $appDebug,
        public string $appUrl,

        // Database (必須)
        public string $dbHost,
        public int $dbPort,
        public string $dbDatabase,
        public string $dbUsername,
        public string $dbPassword,

        // Twitter (オプション)
        public ?string $twitterBearerToken,
        public ?string $twitterClientId,
        public ?string $twitterClientSecret,
        public ?string $twitterConsumerKey,
        public ?string $twitterConsumerSecret,

        // Discord (オプション)
        public ?string $discordToken,
        public ?string $discordChannel,

        // OneSignal (オプション)
        public ?string $onesignalAppId,
        public ?string $onesignalRestApiKey,

        // Dropbox (オプション)
        public ?string $dropboxRefreshToken,
        public ?string $dropboxAppKey,
        public ?string $dropboxAppSecret,

        // Google reCAPTCHA (オプション)
        public ?string $googleRecaptchaProjectName,
        public ?string $googleRecaptchaSiteKey,
        public ?string $googleRecaptchaCredential,

        // Misskey (オプション)
        public ?string $misskeyToken,

        // BlueSky (オプション)
        public ?string $blueskyUser,
        public ?string $blueskyPassword,

        // Google Analytics (オプション)
        public ?string $gtag,

        // Logging (オプション)
        public ?string $logSlackWebhookUrl,
    ) {}

    /**
     * 環境変数から EnvironmentConfig インスタンスを生成
     */
    public static function fromEnv(): self
    {
        return new self(
            // Application
            appName: config('app.name'),
            appEnv: config('app.env'),
            appDebug: config('app.debug'),
            appUrl: config('app.url'),

            // Database
            dbHost: config('database.connections.mysql.host'),
            dbPort: (int) config('database.connections.mysql.port'),
            dbDatabase: config('database.connections.mysql.database'),
            dbUsername: config('database.connections.mysql.username'),
            dbPassword: config('database.connections.mysql.password'),

            // Twitter
            twitterBearerToken: self::nullIfEmpty(config('services.twitter.bearer_token')),
            twitterClientId: self::nullIfEmpty(config('services.twitter.client_id')),
            twitterClientSecret: self::nullIfEmpty(config('services.twitter.client_secret')),
            twitterConsumerKey: self::nullIfEmpty(config('services.twitter.consumer_key')),
            twitterConsumerSecret: self::nullIfEmpty(config('services.twitter.consumer_secret')),

            // Discord
            discordToken: self::nullIfEmpty(config('services.discord.token')),
            discordChannel: self::nullIfEmpty(config('services.discord.channel')),

            // OneSignal
            onesignalAppId: self::nullIfEmpty(config('onesignal.app_id')),
            onesignalRestApiKey: self::nullIfEmpty(config('onesignal.rest_api_key')),

            // Dropbox
            dropboxRefreshToken: self::nullIfEmpty(config('filesystems.disks.dropbox.refreshToken')),
            dropboxAppKey: self::nullIfEmpty(config('filesystems.disks.dropbox.appKey')),
            dropboxAppSecret: self::nullIfEmpty(config('filesystems.disks.dropbox.appSecret')),

            // Google reCAPTCHA
            googleRecaptchaProjectName: self::nullIfEmpty(config('services.google_recaptcha.projectName')),
            googleRecaptchaSiteKey: self::nullIfEmpty(config('services.google_recaptcha.siteKey')),
            googleRecaptchaCredential: self::nullIfEmpty(config('services.google_recaptcha.credential')),

            // Misskey
            misskeyToken: self::nullIfEmpty(config('services.misskey.token')),

            // BlueSky
            blueskyUser: self::nullIfEmpty(config('services.bluesky.user')),
            blueskyPassword: self::nullIfEmpty(config('services.bluesky.password')),

            // Google Analytics
            gtag: self::nullIfEmpty(config('app.gtag')),

            // Logging
            logSlackWebhookUrl: self::nullIfEmpty(config('logging.channels.slack.url')),
        );
    }

    /**
     * Twitter 連携が有効かどうか
     */
    public function hasTwitter(): bool
    {
        return $this->twitterBearerToken !== null
            && $this->twitterClientId !== null
            && $this->twitterClientSecret !== null;
    }

    /**
     * Discord 連携が有効かどうか
     */
    public function hasDiscord(): bool
    {
        return $this->discordToken !== null && $this->discordChannel !== null;
    }

    /**
     * OneSignal プッシュ通知が有効かどうか
     */
    public function hasOneSignal(): bool
    {
        return $this->onesignalAppId !== null && $this->onesignalRestApiKey !== null;
    }

    /**
     * Dropbox バックアップが有効かどうか
     */
    public function hasDropbox(): bool
    {
        return $this->dropboxRefreshToken !== null
            && $this->dropboxAppKey !== null
            && $this->dropboxAppSecret !== null;
    }

    /**
     * Google reCAPTCHA が有効かどうか
     */
    public function hasRecaptcha(): bool
    {
        return $this->googleRecaptchaProjectName !== null
            && $this->googleRecaptchaSiteKey !== null
            && $this->googleRecaptchaCredential !== null;
    }

    /**
     * Misskey 連携が有効かどうか
     */
    public function hasMisskey(): bool
    {
        return $this->misskeyToken !== null;
    }

    /**
     * BlueSky 連携が有効かどうか
     */
    public function hasBlueSky(): bool
    {
        return $this->blueskyUser !== null && $this->blueskyPassword !== null;
    }

    /**
     * Google Analytics が有効かどうか
     */
    public function hasGoogleAnalytics(): bool
    {
        return $this->gtag !== null;
    }

    /**
     * 本番環境かどうか
     */
    public function isProduction(): bool
    {
        return $this->appEnv === 'production';
    }

    /**
     * 開発環境かどうか
     */
    public function isDevelopment(): bool
    {
        return $this->appEnv === 'local' || $this->appEnv === 'development';
    }

    /**
     * 空文字列またはダミー値の場合は null を返す
     *
     * config() が返すダミー値（'dummy-*'）を null として扱う
     */
    private static function nullIfEmpty(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // ダミー値の場合は null を返す
        if (str_starts_with($value, 'dummy-')) {
            return null;
        }

        return $value;
    }
}
