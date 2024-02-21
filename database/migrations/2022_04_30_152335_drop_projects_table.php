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
        $this->schema->create('projects', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->comment('プロジェクト名');
            $table->text('redirect');
            $table->text('credential')->comment('認証情報');
            $table->timestamps();
        });
        $this->schema->create('project_users', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('uid')->comment('Firebaseで付与されるUID');
            $table->timestamps();
        });
    }
}
