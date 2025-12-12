<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\OauthTokenRepository;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

final class GetTokenTest extends TestCase
{
    private OauthTokenRepository $oauthTokenRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->oauthTokenRepository = app(OauthTokenRepository::class);
    }

    public function test(): void
    {
        $token = OauthToken::create([
            'application' => 'dummy app',
            'token_type' => 'dummy type',
            'scope' => 'dummy scope',
            'access_token' => '123',
            'refresh_token' => '456',
            'expired_at' => now(),
        ]);
        $oauthToken = $this->oauthTokenRepository->getToken($token->application);

        $this->assertSame($token->application, $oauthToken->application);
    }

    public function test_存在しないトークンはエラー(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->oauthTokenRepository->getToken('missing');
    }

    public function test_twitterトークン取得(): void
    {
        $token = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'twitter_access_token',
            'refresh_token' => 'twitter_refresh_token',
            'expired_at' => now()->addHours(2),
        ]);

        $oauthToken = $this->oauthTokenRepository->getToken('twitter');

        $this->assertInstanceOf(OauthToken::class, $oauthToken);
        $this->assertSame('twitter', $oauthToken->application);
        $this->assertSame('bearer', $oauthToken->token_type);
        $this->assertSame('twitter_access_token', $oauthToken->access_token);
        $this->assertSame('twitter_refresh_token', $oauthToken->refresh_token);
    }

    public function test_discordトークン取得(): void
    {
        $token = OauthToken::create([
            'application' => 'discord',
            'token_type' => 'Bearer',
            'scope' => 'identify guilds',
            'access_token' => 'discord_access_token',
            'refresh_token' => 'discord_refresh_token',
            'expired_at' => now()->addWeek(),
        ]);

        $oauthToken = $this->oauthTokenRepository->getToken('discord');

        $this->assertInstanceOf(OauthToken::class, $oauthToken);
        $this->assertSame('discord', $oauthToken->application);
        $this->assertSame('Bearer', $oauthToken->token_type);
        $this->assertSame('discord_access_token', $oauthToken->access_token);
    }

    public function test_blue_skyトークン取得(): void
    {
        $token = OauthToken::create([
            'application' => 'bluesky',
            'token_type' => 'bearer',
            'scope' => 'app.bsky.feed.post',
            'access_token' => 'bluesky_access_token',
            'refresh_token' => 'bluesky_refresh_token',
            'expired_at' => now()->addDay(),
        ]);

        $oauthToken = $this->oauthTokenRepository->getToken('bluesky');

        $this->assertInstanceOf(OauthToken::class, $oauthToken);
        $this->assertSame('bluesky', $oauthToken->application);
        $this->assertSame('bluesky_access_token', $oauthToken->access_token);
    }

    public function test_期限切れトークンも取得可能(): void
    {
        $token = OauthToken::create([
            'application' => 'expired_app',
            'token_type' => 'bearer',
            'scope' => 'read',
            'access_token' => 'expired_token',
            'refresh_token' => 'refresh_token',
            'expired_at' => now()->subHour(),
        ]);

        $oauthToken = $this->oauthTokenRepository->getToken('expired_app');

        $this->assertInstanceOf(OauthToken::class, $oauthToken);
        $this->assertTrue($oauthToken->isExpired());
    }

    public function test_複数トークン存在時に正しいトークンを取得(): void
    {
        // 複数のトークンを作成
        OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read',
            'access_token' => 'twitter_token',
            'refresh_token' => 'twitter_refresh',
            'expired_at' => now()->addHours(2),
        ]);

        OauthToken::create([
            'application' => 'discord',
            'token_type' => 'Bearer',
            'scope' => 'identify',
            'access_token' => 'discord_token',
            'refresh_token' => 'discord_refresh',
            'expired_at' => now()->addWeek(),
        ]);

        OauthToken::create([
            'application' => 'bluesky',
            'token_type' => 'bearer',
            'scope' => 'app.bsky.feed.post',
            'access_token' => 'bluesky_token',
            'refresh_token' => 'bluesky_refresh',
            'expired_at' => now()->addDay(),
        ]);

        // 各トークンが正しく取得できることを確認
        $twitterToken = $this->oauthTokenRepository->getToken('twitter');
        $this->assertSame('twitter', $twitterToken->application);
        $this->assertSame('twitter_token', $twitterToken->access_token);

        $discordToken = $this->oauthTokenRepository->getToken('discord');
        $this->assertSame('discord', $discordToken->application);
        $this->assertSame('discord_token', $discordToken->access_token);

        $blueskyToken = $this->oauthTokenRepository->getToken('bluesky');
        $this->assertSame('bluesky', $blueskyToken->application);
        $this->assertSame('bluesky_token', $blueskyToken->access_token);
    }
}
