<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\OauthToken;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

class OauthTokenTest extends TestCase
{
    public function test_access_tokenとrefresh_tokenは暗号化されて保存され復号して取得できる(): void
    {
        $token = OauthToken::create([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read',
            'access_token' => 'secret_access_token',
            'refresh_token' => 'secret_refresh_token',
            'expired_at' => now()->addHour(),
        ]);

        $this->assertSame('secret_access_token', $token->fresh()->access_token);
        $this->assertSame('secret_refresh_token', $token->fresh()->refresh_token);

        $rawAccessToken = DB::table('oauth_tokens')->where('application', 'twitter')->value('access_token');
        $rawRefreshToken = DB::table('oauth_tokens')->where('application', 'twitter')->value('refresh_token');

        $this->assertNotSame('secret_access_token', $rawAccessToken);
        $this->assertNotSame('secret_refresh_token', $rawRefreshToken);
    }

    public function test_access_tokenとrefresh_tokenはシリアライズ結果から除外される(): void
    {
        $token = OauthToken::create([
            'application' => 'discord',
            'token_type' => 'Bearer',
            'scope' => 'identify',
            'access_token' => 'secret_access_token',
            'refresh_token' => 'secret_refresh_token',
            'expired_at' => now()->addHour(),
        ]);

        $array = $token->toArray();

        $this->assertArrayNotHasKey('access_token', $array);
        $this->assertArrayNotHasKey('refresh_token', $array);
    }
}
