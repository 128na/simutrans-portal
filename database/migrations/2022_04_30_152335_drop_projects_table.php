<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProjectsTable extends Migration
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
        $this->schema->dropIfExists('project_users');
        $this->schema->dropIfExists('projects');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->create('projects', static function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $blueprint->string('name')->comment('プロジェクト名');
            $blueprint->text('redirect');
            $blueprint->text('credential')->comment('認証情報');
            $blueprint->timestamps();
        });
        $this->schema->create('project_users', static function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('project_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('uid')->comment('Firebaseで付与されるUID');
            $blueprint->timestamps();
        });
    }
}
