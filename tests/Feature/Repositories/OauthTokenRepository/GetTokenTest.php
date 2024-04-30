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
        $result = $this->oauthTokenRepository->getToken($token->application);

        $this->assertSame($token->application, $result->application);
    }

    public function test_存在しないトークンはエラー(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->oauthTokenRepository->getToken('missing');
    }
}
