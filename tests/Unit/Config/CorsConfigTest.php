<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use RuntimeException;
use Tests\Unit\TestCase;

class CorsConfigTest extends TestCase
{
    private string $configPath;

    private false|string $originalCorsAllowedOrigins;

    private false|string $originalAppUrl;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->configPath = dirname(__DIR__, 3).'/config/cors.php';
        $this->originalCorsAllowedOrigins = getenv('CORS_ALLOWED_ORIGINS');
        $this->originalAppUrl = getenv('APP_URL');
    }

    #[\Override]
    protected function tearDown(): void
    {
        $this->restoreEnv('CORS_ALLOWED_ORIGINS', $this->originalCorsAllowedOrigins);
        $this->restoreEnv('APP_URL', $this->originalAppUrl);

        parent::tearDown();
    }

    public function test_未設定の場合はデフォルトのオリジンへフォールバックする(): void
    {
        $this->setEnv('CORS_ALLOWED_ORIGINS', false);
        $this->setEnv('APP_URL', 'https://example.test');

        $config = include $this->configPath;

        $this->assertSame(['https://example.test'], $config['allowed_origins']);
    }

    public function test_空文字の場合もデフォルトのオリジンへフォールバックする(): void
    {
        $this->setEnv('CORS_ALLOWED_ORIGINS', '');
        $this->setEnv('APP_URL', 'https://example.test');

        $config = include $this->configPath;

        $this->assertSame(['https://example.test'], $config['allowed_origins']);
    }

    public function test_カンマ区切りの複数オリジンを許可リストとして解決する(): void
    {
        $this->setEnv('CORS_ALLOWED_ORIGINS', 'https://a.example,https://b.example');

        $config = include $this->configPath;

        $this->assertSame(['https://a.example', 'https://b.example'], $config['allowed_origins']);
    }

    public function test_ワイルドカードを設定すると例外を送出する(): void
    {
        $this->setEnv('CORS_ALLOWED_ORIGINS', '*');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('CORS_ALLOWED_ORIGINS must not contain "*"');

        include $this->configPath;
    }

    public function test_supports_credentialsは常に有効である(): void
    {
        $this->setEnv('CORS_ALLOWED_ORIGINS', 'https://a.example');

        $config = include $this->configPath;

        $this->assertTrue($config['supports_credentials']);
    }

    /**
     * putenv() だけでは $_ENV/$_SERVER に既存の値がある場合に上書きされないため、
     * env() が参照する全ソース(putenv/$_ENV/$_SERVER)を揃えて上書きする。
     */
    private function setEnv(string $key, false|string $value): void
    {
        if ($value === false) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            return;
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    private function restoreEnv(string $key, false|string $originalValue): void
    {
        $this->setEnv($key, $originalValue);
    }
}
