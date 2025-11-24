<?php

declare(strict_types=1);

namespace App\Config;

use Illuminate\Support\Facades\Config;

/**
 * 型安全な環境変数アクセスクラス
 *
 * 環境変数を型安全にアクセスするための読み取り専用設定クラス。
 * 実行時エラーを防ぎ、IDEの補完サポートを提供します。
 */
final readonly class EnvironmentConfig
{
    public function __construct(
        // アプリケーション設定（必須）
        public string $appName,
        public string $appEnv,
        public bool $appDebug,
        public string $appUrl,
        public ?string $assetUrl,

        // データベース設定（必須）
        public string $dbHost,
        public int $dbPort,
        public string $dbDatabase,
        public string $dbUsername,
        public string $dbPassword,

        // ログ設定（オプション）
        public ?string $logSlackWebhookUrl,

        // メール設定（必須）
        public string $mailMailer,
        public string $mailHost,
        public int $mailPort,

        // Twitter設定（オプション）
        public ?string $twitterBearerToken,
        public ?string $twitterClientId,
        public ?string $twitterClientSecret,
        public ?string $twitterConsumerKey,
        public ?string $twitterConsumerSecret,

        // Discord設定（オプション）
        public ?string $discordToken,
        public ?string $discordChannel,
        public string $discordDomain,
        public int $discordMaxAge,
        public int $discordMaxUses,

        // Google設定（オプション）
        public ?string $googleRecaptchaProjectName,
        public ?string $googleRecaptchaSiteKey,
        public ?string $googleRecaptchaCredential,
        public ?string $gtag,

        // OneSignal設定（オプション）
        public ?string $onesignalAppId,
        public ?string $onesignalRestApiKey,

        // Dropbox設定（オプション）
        public ?string $dropboxAuthorizationToken,

        // Misskey設定（オプション）
        public string $misskeyBaseUrl,
        public ?string $misskeyToken,

        // BlueSky設定（オプション）
        public ?string $blueskyUser,
        public ?string $blueskyPassword,
    ) {}

    /**
     * 環境変数から EnvironmentConfig インスタンスを生成
     */
    public static function fromEnv(): self
    {
        return new self(
            // アプリケーション設定
            appName: Config::string('app.name'),
            appEnv: Config::string('app.env'),
            appDebug: Config::boolean('app.debug'),
            appUrl: Config::string('app.url'),
            assetUrl: Config::string('app.asset_url', ''),

            // データベース設定
            dbHost: Config::string('database.connections.mysql.host', '127.0.0.1'),
            dbPort: Config::integer('database.connections.mysql.port', 3306),
            dbDatabase: Config::string('database.connections.mysql.database', 'forge'),
            dbUsername: Config::string('database.connections.mysql.username', 'forge'),
            dbPassword: Config::string('database.connections.mysql.password', ''),

            // ログ設定
            logSlackWebhookUrl: Config::string('logging.channels.slack.url', ''),

            // メール設定
            mailMailer: Config::string('mail.default', 'smtp'),
            mailHost: Config::string('mail.mailers.smtp.host', 'localhost'),
            mailPort: Config::integer('mail.mailers.smtp.port', 1025),

            // Twitter設定
            twitterBearerToken: Config::string('services.twitter.bearer_token', ''),
            twitterClientId: Config::string('services.twitter.client_id', ''),
            twitterClientSecret: Config::string('services.twitter.client_secret', ''),
            twitterConsumerKey: Config::string('services.twitter.consumer_key', ''),
            twitterConsumerSecret: Config::string('services.twitter.consumer_secret', ''),

            // Discord設定
            discordToken: Config::string('services.discord.token', ''),
            discordChannel: Config::string('services.discord.channel', ''),
            discordDomain: Config::string('services.discord.domain', 'https://discord.gg'),
            discordMaxAge: Config::integer('services.discord.max_age', 300),
            discordMaxUses: Config::integer('services.discord.max_uses', 1),

            // Google設定
            googleRecaptchaProjectName: Config::string('services.google_recaptcha.projectName', ''),
            googleRecaptchaSiteKey: Config::string('services.google_recaptcha.siteKey', ''),
            googleRecaptchaCredential: Config::string('services.google_recaptcha.credential', ''),
            gtag: Config::string('app.gtag', ''),

            // OneSignal設定
            onesignalAppId: Config::string('onesignal.app_id', ''),
            onesignalRestApiKey: Config::string('onesignal.rest_api_key', ''),

            // Dropbox設定
            dropboxAuthorizationToken: Config::string('filesystems.disks.dropbox.authorization_token', ''),

            // Misskey設定
            misskeyBaseUrl: Config::string('services.misskey.base_url', 'https://misskey.io/api'),
            misskeyToken: Config::string('services.misskey.token', ''),

            // BlueSky設定
            blueskyUser: Config::string('services.bluesky.user', ''),
            blueskyPassword: Config::string('services.bluesky.password', ''),
        );
    }

    /**
     * Twitter連携が有効かチェック
     */
    public function hasTwitter(): bool
    {
        return $this->twitterBearerToken !== null && $this->twitterBearerToken !== ''
            && $this->twitterClientId !== null && $this->twitterClientId !== ''
            && $this->twitterClientSecret !== null && $this->twitterClientSecret !== ''
            && $this->twitterConsumerKey !== null && $this->twitterConsumerKey !== ''
            && $this->twitterConsumerSecret !== null && $this->twitterConsumerSecret !== '';
    }

    /**
     * Discord連携が有効かチェック
     */
    public function hasDiscord(): bool
    {
        return $this->discordToken !== null && $this->discordToken !== ''
            && $this->discordChannel !== null && $this->discordChannel !== '';
    }

    /**
     * Google reCAPTCHA が有効かチェック
     */
    public function hasGoogleRecaptcha(): bool
    {
        return $this->googleRecaptchaProjectName !== null && $this->googleRecaptchaProjectName !== ''
            && $this->googleRecaptchaSiteKey !== null && $this->googleRecaptchaSiteKey !== ''
            && $this->googleRecaptchaCredential !== null && $this->googleRecaptchaCredential !== '';
    }

    /**
     * OneSignal プッシュ通知が有効かチェック
     */
    public function hasOneSignal(): bool
    {
        return $this->onesignalAppId !== null && $this->onesignalAppId !== ''
            && $this->onesignalRestApiKey !== null && $this->onesignalRestApiKey !== '';
    }

    /**
     * Dropbox バックアップが有効かチェック
     */
    public function hasDropbox(): bool
    {
        return $this->dropboxAuthorizationToken !== null && $this->dropboxAuthorizationToken !== '';
    }

    /**
     * Misskey連携が有効かチェック
     */
    public function hasMisskey(): bool
    {
        return $this->misskeyToken !== null && $this->misskeyToken !== '';
    }

    /**
     * BlueSky連携が有効かチェック
     */
    public function hasBlueSky(): bool
    {
        return $this->blueskyUser !== null && $this->blueskyUser !== ''
            && $this->blueskyPassword !== null && $this->blueskyPassword !== '';
    }

    /**
     * Google Analytics (gtag) が有効かチェック
     */
    public function hasGoogleAnalytics(): bool
    {
        return $this->gtag !== null && $this->gtag !== '';
    }

    /**
     * Slack通知が有効かチェック
     */
    public function hasSlackLogging(): bool
    {
        return $this->logSlackWebhookUrl !== null && $this->logSlackWebhookUrl !== '';
    }
}
