<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOauth extends Migration
{
    public $schema;

    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->schema->dropIfExists('oauth_access_tokens');
        $this->schema->dropIfExists('oauth_auth_codes');
        $this->schema->dropIfExists('oauth_clients');
        $this->schema->dropIfExists('oauth_personal_access_clients');
        $this->schema->dropIfExists('oauth_refresh_tokens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->create('oauth_auth_codes', static function (Blueprint $blueprint) : void {
            $blueprint->string('id', 100)->primary();
            $blueprint->unsignedBigInteger('user_id')->index();
            $blueprint->uuid('client_id');
            $blueprint->text('scopes')->nullable();
            $blueprint->boolean('revoked');
            $blueprint->dateTime('expires_at')->nullable();
        });
        $this->schema->create('oauth_access_tokens', static function (Blueprint $blueprint) : void {
            $blueprint->string('id', 100)->primary();
            $blueprint->unsignedBigInteger('user_id')->nullable()->index();
            $blueprint->uuid('client_id');
            $blueprint->string('name')->nullable();
            $blueprint->text('scopes')->nullable();
            $blueprint->boolean('revoked');
            $blueprint->timestamps();
            $blueprint->dateTime('expires_at')->nullable();
        });
        $this->schema->create('oauth_refresh_tokens', static function (Blueprint $blueprint) : void {
            $blueprint->string('id', 100)->primary();
            $blueprint->string('access_token_id', 100)->index();
            $blueprint->boolean('revoked');
            $blueprint->dateTime('expires_at')->nullable();
        });
        $this->schema->create('oauth_clients', static function (Blueprint $blueprint) : void {
            $blueprint->uuid('id')->primary();
            $blueprint->unsignedBigInteger('user_id')->nullable()->index();
            $blueprint->string('name');
            $blueprint->string('secret', 100)->nullable();
            $blueprint->string('provider')->nullable();
            $blueprint->text('redirect');
            $blueprint->boolean('personal_access_client');
            $blueprint->boolean('password_client');
            $blueprint->boolean('revoked');
            $blueprint->timestamps();
        });
        $this->schema->create('oauth_personal_access_clients', static function (Blueprint $blueprint) : void {
            $blueprint->bigIncrements('id');
            $blueprint->uuid('client_id');
            $blueprint->timestamps();
        });
    }
}
