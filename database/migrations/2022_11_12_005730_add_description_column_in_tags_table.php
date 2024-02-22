<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnInTagsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $blueprint): void {
            $blueprint->text('description')->nullable()->after('name')->comment('説明');
            $blueprint->boolean('editable')->after('description')->default(true)->comment('1:編集可,0:編集不可');
            $blueprint->unsignedBigInteger('created_by')->nullable()->after('editable');
            $blueprint->unsignedBigInteger('last_modified_by')->nullable()->after('created_by');
            $blueprint->timestamp('last_modified_at')->nullable()->after('last_modified_by');
            $blueprint->foreign('created_by')->nullOnDelete()->references('id')->on('users');
            $blueprint->foreign('last_modified_by')->nullOnDelete()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['created_by']);
            $blueprint->dropForeign(['last_modified_by']);
            $blueprint->dropColumn([
                'description',
                'editable',
                'created_by',
                'last_modified_by',
            ]);
        });
    }
}
