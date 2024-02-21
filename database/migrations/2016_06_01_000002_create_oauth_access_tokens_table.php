<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthAccessTokensTable extends Migration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists('oauth_access_tokens');
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return config('passport.storage.database.connection');
    }
}
