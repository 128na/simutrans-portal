<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use App\Config\EnvironmentConfig;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class EnvironmentConfigTest extends TestCase
{
    public function test_fromEnv_creates_instance_with_correct_values(): void
    {
        Config::set('app.name', 'Test App');
        Config::set('app.env', 'testing');
        Config::set('app.debug', true);
        Config::set('app.url', 'http://test.local');

        $config = EnvironmentConfig::fromEnv();

        $this->assertInstanceOf(EnvironmentConfig::class, $config);
        $this->assertSame('Test App', $config->appName);
        $this->assertSame('testing', $config->appEnv);
        $this->assertTrue($config->appDebug);
        $this->assertSame('http://test.local', $config->appUrl);
    }

    public function test_hasTwitter_returns_true_when_all_credentials_set(): void
    {
        Config::set('services.twitter.bearer_token', 'test-bearer');
        Config::set('services.twitter.client_id', 'test-client-id');
        Config::set('services.twitter.client_secret', 'test-client-secret');
        Config::set('services.twitter.consumer_key', 'test-consumer-key');
        Config::set('services.twitter.consumer_secret', 'test-consumer-secret');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasTwitter());
    }

    public function test_hasTwitter_returns_false_when_credentials_missing(): void
    {
        Config::set('services.twitter.bearer_token', null);
        Config::set('services.twitter.client_id', null);
        Config::set('services.twitter.client_secret', null);
        Config::set('services.twitter.consumer_key', null);
        Config::set('services.twitter.consumer_secret', null);

        $config = EnvironmentConfig::fromEnv();

        $this->assertFalse($config->hasTwitter());
    }

    public function test_hasDiscord_returns_true_when_credentials_set(): void
    {
        Config::set('services.discord.token', 'test-token');
        Config::set('services.discord.channel', 'test-channel');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasDiscord());
    }

    public function test_hasDiscord_returns_false_when_credentials_missing(): void
    {
        Config::set('services.discord.token', null);
        Config::set('services.discord.channel', null);

        $config = EnvironmentConfig::fromEnv();

        $this->assertFalse($config->hasDiscord());
    }

    public function test_hasGoogleRecaptcha_returns_true_when_credentials_set(): void
    {
        Config::set('services.google_recaptcha.projectName', 'test-project');
        Config::set('services.google_recaptcha.siteKey', 'test-site-key');
        Config::set('services.google_recaptcha.credential', 'test-credential.json');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasGoogleRecaptcha());
    }

    public function test_hasOneSignal_returns_true_when_credentials_set(): void
    {
        Config::set('onesignal.app_id', 'test-app-id');
        Config::set('onesignal.rest_api_key', 'test-api-key');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasOneSignal());
    }

    public function test_hasDropbox_returns_true_when_token_set(): void
    {
        Config::set('filesystems.disks.dropbox.authorization_token', 'test-token');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasDropbox());
    }

    public function test_hasMisskey_returns_true_when_token_set(): void
    {
        Config::set('services.misskey.token', 'test-token');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasMisskey());
    }

    public function test_hasBlueSky_returns_true_when_credentials_set(): void
    {
        Config::set('services.bluesky.user', 'test-user');
        Config::set('services.bluesky.password', 'test-password');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasBlueSky());
    }

    public function test_hasGoogleAnalytics_returns_true_when_gtag_set(): void
    {
        Config::set('app.gtag', 'G-XXXXXXXXXX');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasGoogleAnalytics());
    }

    public function test_hasSlackLogging_returns_true_when_webhook_url_set(): void
    {
        Config::set('logging.channels.slack.url', 'https://hooks.slack.com/test');

        $config = EnvironmentConfig::fromEnv();

        $this->assertTrue($config->hasSlackLogging());
    }

    public function test_readonly_properties_cannot_be_modified(): void
    {
        $config = EnvironmentConfig::fromEnv();

        $this->expectException(\Error::class);
        $config->appName = 'Modified'; // @phpstan-ignore-line
    }
}
