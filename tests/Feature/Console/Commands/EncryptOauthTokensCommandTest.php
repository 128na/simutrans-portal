<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\EncryptOauthTokensCommand;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

class EncryptOauthTokensCommandTest extends TestCase
{
    public function test_平文トークンを暗号化する(): void
    {
        DB::table('oauth_tokens')->insert([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read',
            'access_token' => 'plain_access_token',
            'refresh_token' => 'plain_refresh_token',
            'expired_at' => now()->addHour(),
        ]);

        $this->artisan('oauth-tokens:encrypt-existing')->assertSuccessful();

        $row = DB::table('oauth_tokens')->where('application', 'twitter')->first();
        $this->assertSame('plain_access_token', Crypt::decryptString($row->access_token));
        $this->assertSame('plain_refresh_token', Crypt::decryptString($row->refresh_token));
    }

    public function test_既に暗号化済みのトークンは再暗号化されずスキップされる(): void
    {
        DB::table('oauth_tokens')->insert([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read',
            'access_token' => Crypt::encryptString('already_encrypted_access_token'),
            'refresh_token' => Crypt::encryptString('already_encrypted_refresh_token'),
            'expired_at' => now()->addHour(),
        ]);

        $this->artisan('oauth-tokens:encrypt-existing')->assertSuccessful();

        $row = DB::table('oauth_tokens')->where('application', 'twitter')->first();
        $this->assertSame('already_encrypted_access_token', Crypt::decryptString($row->access_token));
        $this->assertSame('already_encrypted_refresh_token', Crypt::decryptString($row->refresh_token));
    }

    public function test_2回実行しても値が壊れない(): void
    {
        DB::table('oauth_tokens')->insert([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read',
            'access_token' => 'plain_access_token',
            'refresh_token' => 'plain_refresh_token',
            'expired_at' => now()->addHour(),
        ]);

        $this->artisan('oauth-tokens:encrypt-existing')->assertSuccessful();
        $this->artisan('oauth-tokens:encrypt-existing')->assertSuccessful();

        $row = DB::table('oauth_tokens')->where('application', 'twitter')->first();
        $this->assertSame('plain_access_token', Crypt::decryptString($row->access_token));
        $this->assertSame('plain_refresh_token', Crypt::decryptString($row->refresh_token));
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(EncryptOauthTokensCommand::class);

        $this->assertSame('oauth-tokens:encrypt-existing', $command->getName());
    }

    public function test_command_description_exists(): void
    {
        $command = $this->app->make(EncryptOauthTokensCommand::class);

        $this->assertNotEmpty($command->getDescription());
    }
}
