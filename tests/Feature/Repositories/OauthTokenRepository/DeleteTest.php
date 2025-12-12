<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\OauthTokenRepository;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use Tests\Feature\TestCase;

final class DeleteTest extends TestCase
{
    private OauthTokenRepository $oauthTokenRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->oauthTokenRepository = app(OauthTokenRepository::class);
    }

    public function test_トークン削除(): void
    {
        $token = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write',
            'access_token' => 'test_access_token',
            'refresh_token' => 'test_refresh_token',
            'expired_at' => now()->addHours(2),
        ]);

        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'twitter',
        ]);

        $this->oauthTokenRepository->delete($token);

        $this->assertDatabaseMissing('oauth_tokens', [
            'application' => 'twitter',
        ]);
    }

    public function test_複数トークンから特定のトークンのみ削除(): void
    {
        // 複数のトークンを作成
        $twitterToken = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write',
            'access_token' => 'twitter_token',
            'refresh_token' => 'twitter_refresh',
            'expired_at' => now()->addHours(2),
        ]);

        $discordToken = OauthToken::create([
            'application' => 'discord',
            'token_type' => 'Bearer',
            'scope' => 'identify',
            'access_token' => 'discord_token',
            'refresh_token' => 'discord_refresh',
            'expired_at' => now()->addWeek(),
        ]);

        $blueskyToken = OauthToken::create([
            'application' => 'bluesky',
            'token_type' => 'bearer',
            'scope' => 'app.bsky.feed.post',
            'access_token' => 'bluesky_token',
            'refresh_token' => 'bluesky_refresh',
            'expired_at' => now()->addDay(),
        ]);

        $this->assertDatabaseCount('oauth_tokens', 3);

        // Twitterトークンのみ削除
        $this->oauthTokenRepository->delete($twitterToken);

        $this->assertDatabaseCount('oauth_tokens', 2);
        $this->assertDatabaseMissing('oauth_tokens', [
            'application' => 'twitter',
        ]);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'discord',
        ]);
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'bluesky',
        ]);
    }

    public function test_期限切れトークンの削除(): void
    {
        $expiredToken = OauthToken::create([
            'application' => 'expired_service',
            'token_type' => 'bearer',
            'scope' => 'read',
            'access_token' => 'expired_access',
            'refresh_token' => 'expired_refresh',
            'expired_at' => now()->subHour(),
        ]);

        $this->assertTrue($expiredToken->isExpired());
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'expired_service',
        ]);

        $this->oauthTokenRepository->delete($expiredToken);

        $this->assertDatabaseMissing('oauth_tokens', [
            'application' => 'expired_service',
        ]);
    }

    public function test_連携解除シナリオ(): void
    {
        // ユーザーがTwitter連携を解除するシナリオ
        $twitterToken = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'active_twitter_token',
            'refresh_token' => 'active_twitter_refresh',
            'expired_at' => now()->addHours(2),
        ]);

        // トークンは有効な状態
        $this->assertFalse($twitterToken->isExpired());
        $this->assertDatabaseHas('oauth_tokens', [
            'application' => 'twitter',
            'access_token' => 'active_twitter_token',
        ]);

        // ユーザーが連携を解除
        $this->oauthTokenRepository->delete($twitterToken);

        // トークンが削除されていることを確認
        $this->assertDatabaseMissing('oauth_tokens', [
            'application' => 'twitter',
        ]);
    }
}
