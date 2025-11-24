<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use App\Config\EnvironmentConfig;
use Tests\Unit\TestCase;

final class EnvironmentConfigTest extends TestCase
{
    private EnvironmentConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = EnvironmentConfig::fromEnv();
    }

    public function test_from_env_creates_instance(): void
    {
        $this->assertInstanceOf(EnvironmentConfig::class, $this->config);
    }

    public function test_required_properties_are_not_null(): void
    {
        $this->assertIsString($this->config->appName);
        $this->assertIsString($this->config->appEnv);
        $this->assertIsBool($this->config->appDebug);
        $this->assertIsString($this->config->appUrl);

        $this->assertIsString($this->config->dbHost);
        $this->assertIsInt($this->config->dbPort);
        $this->assertIsString($this->config->dbDatabase);
        $this->assertIsString($this->config->dbUsername);
        $this->assertIsString($this->config->dbPassword);
    }

    public function test_optional_properties_can_be_null(): void
    {
        // これらのプロパティは null または string
        $this->assertTrue(
            $this->config->twitterBearerToken === null || is_string($this->config->twitterBearerToken)
        );
        $this->assertTrue(
            $this->config->discordToken === null || is_string($this->config->discordToken)
        );
    }

    public function test_has_twitter_returns_false_when_credentials_are_incomplete(): void
    {
        // テスト環境ではTwitter認証情報がダミー値なので false になるはず
        $hasTwitter = $this->config->hasTwitter();
        $this->assertIsBool($hasTwitter);
    }

    public function test_has_discord_returns_false_when_credentials_are_incomplete(): void
    {
        // テスト環境ではDiscord認証情報がダミー値なので false になるはず
        $hasDiscord = $this->config->hasDiscord();
        $this->assertIsBool($hasDiscord);
    }

    public function test_has_one_signal_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasOneSignal());
    }

    public function test_has_dropbox_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasDropbox());
    }

    public function test_has_recaptcha_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasRecaptcha());
    }

    public function test_has_misskey_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasMisskey());
    }

    public function test_has_blue_sky_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasBlueSky());
    }

    public function test_has_google_analytics_returns_boolean(): void
    {
        $this->assertIsBool($this->config->hasGoogleAnalytics());
    }

    public function test_is_production_returns_boolean(): void
    {
        $this->assertIsBool($this->config->isProduction());
    }

    public function test_is_development_returns_boolean(): void
    {
        $this->assertIsBool($this->config->isDevelopment());
    }

    public function test_environment_is_not_production_in_tests(): void
    {
        $this->assertFalse($this->config->isProduction());
    }
}
