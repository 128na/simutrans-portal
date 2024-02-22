<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthClientsTable extends Migration
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
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return config('passport.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->schema->create('oauth_clients', function (Blueprint $blueprint): void {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists('oauth_clients');
    }
}
