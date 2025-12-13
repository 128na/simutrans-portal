<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\OauthTokenRepository;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use Tests\Feature\TestCase;

class UpdateOrCreateTest extends TestCase
{
    private OauthTokenRepository $oauthTokenRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->oauthTokenRepository = app(OauthTokenRepository::class);
    }

    public function test_新規トークン作成(): void
    {
        $application = 'twitter';
        $data = [
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'test_access_token_123',
            'refresh_token' => 'test_refresh_token_456',
            'expired_at' => now()->addHours(2),
        ];

        $token = $this->oauthTokenRepository->updateOrCreate(
            ['application' => $application],
            $data
        );

        $this->assertInstanceOf(OauthToken::class, $token);
        $this->assertSame($application, $token->application);
        $this->assertSame('bearer', $token->token_type);
        $this->assertSame('tweet.read tweet.write users.read offline.access', $token->scope);
        $this->assertSame('test_access_token_123', $token->access_token);
        $this->assertSame('test_refresh_token_456', $token->refresh_token);
        $this->assertNotNull($token->expired_at);

        // データベースに保存されていることを確認
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => $application,
            'access_token' => 'test_access_token_123',
        ]);
    }

    public function test_既存トークン更新(): void
    {
        // 既存のトークンを作成
        $application = 'twitter';
        $existingToken = OauthToken::create([
            'application' => $application,
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write',
            'access_token' => 'old_access_token',
            'refresh_token' => 'old_refresh_token',
            'expired_at' => now()->addHour(),
        ]);

        // 新しいトークン情報で更新
        $newData = [
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'new_access_token_789',
            'refresh_token' => 'new_refresh_token_012',
            'expired_at' => now()->addHours(3),
        ];

        $updatedToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => $application],
            $newData
        );

        // 同じレコードが更新されていることを確認
        $this->assertSame($existingToken->application, $updatedToken->application);
        $this->assertSame('new_access_token_789', $updatedToken->access_token);
        $this->assertSame('new_refresh_token_012', $updatedToken->refresh_token);

        // データベースには1つだけ存在することを確認
        $this->assertDatabaseCount('oauth_tokens', 1);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => $application,
            'access_token' => 'new_access_token_789',
        ]);
        $this->assertDatabaseMissing('oauth_tokens', [
            'application' => $application,
            'access_token' => 'old_access_token',
        ]);
    }

    public function test_複数プロバイダーの独立管理(): void
    {
        // Twitterトークンを作成
        $twitterToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => 'bearer',
                'scope' => 'tweet.read tweet.write',
                'access_token' => 'twitter_access_token',
                'refresh_token' => 'twitter_refresh_token',
                'expired_at' => now()->addHours(2),
            ]
        );

        // Discordトークンを作成
        $discordToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'discord'],
            [
                'token_type' => 'Bearer',
                'scope' => 'identify guilds',
                'access_token' => 'discord_access_token',
                'refresh_token' => 'discord_refresh_token',
                'expired_at' => now()->addWeek(),
            ]
        );

        // BlueSkyトークンを作成
        $blueskyToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'bluesky'],
            [
                'token_type' => 'bearer',
                'scope' => 'app.bsky.feed.post',
                'access_token' => 'bluesky_access_token',
                'refresh_token' => 'bluesky_refresh_token',
                'expired_at' => now()->addDay(),
            ]
        );

        // 各プロバイダーのトークンが独立して存在することを確認
        $this->assertDatabaseCount('oauth_tokens', 3);
        $this->assertSame('twitter', $twitterToken->application);
        $this->assertSame('discord', $discordToken->application);
        $this->assertSame('bluesky', $blueskyToken->application);

        // Twitterトークンを更新しても他のトークンは影響を受けないことを確認
        $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => 'bearer',
                'scope' => 'tweet.read tweet.write users.read',
                'access_token' => 'twitter_new_access_token',
                'refresh_token' => 'twitter_new_refresh_token',
                'expired_at' => now()->addHours(4),
            ]
        );

        $this->assertDatabaseCount('oauth_tokens', 3);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'twitter',
            'access_token' => 'twitter_new_access_token',
        ]);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'discord',
            'access_token' => 'discord_access_token',
        ]);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'bluesky',
            'access_token' => 'bluesky_access_token',
        ]);
    }

    public function test_トークンリフレッシュシナリオ(): void
    {
        // 期限切れ直前のトークンを作成
        $application = 'twitter';
        $expiredAt = now()->subMinute(); // 1分前に期限切れ

        $oldToken = OauthToken::create([
            'application' => $application,
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write',
            'access_token' => 'expired_access_token',
            'refresh_token' => 'valid_refresh_token',
            'expired_at' => $expiredAt,
        ]);

        $this->assertTrue($oldToken->isExpired());

        // リフレッシュトークンを使って新しいトークンを取得（シミュレーション）
        $refreshedToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => $application],
            [
                'token_type' => 'bearer',
                'scope' => 'tweet.read tweet.write users.read offline.access',
                'access_token' => 'refreshed_access_token',
                'refresh_token' => 'new_refresh_token',
                'expired_at' => now()->addHours(2),
            ]
        );

        $this->assertFalse($refreshedToken->isExpired());
        $this->assertSame('refreshed_access_token', $refreshedToken->access_token);
        $this->assertSame('new_refresh_token', $refreshedToken->refresh_token);
        $this->assertDatabaseMissing('oauth_tokens', [
            'access_token' => 'expired_access_token',
        ]);
    }

    public function test_最小限のデータでトークン作成(): void
    {
        $token = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'minimal_test'],
            [
                'token_type' => 'bearer',
                'scope' => 'read',
                'access_token' => 'minimal_access',
                'refresh_token' => 'minimal_refresh',
                'expired_at' => now()->addHour(),
            ]
        );

        $this->assertInstanceOf(OauthToken::class, $token);
        $this->assertSame('minimal_test', $token->application);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'minimal_test',
        ]);
    }
}
