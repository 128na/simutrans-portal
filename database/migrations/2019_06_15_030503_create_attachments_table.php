<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ポリモーフィックな添付
 */
class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', static function (Blueprint $blueprint): void {
            $blueprint->bigIncrements('id');
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->unsignedBigInteger('attachmentable_id')->nullable()->comment('添付先ID');
            $blueprint->string('attachmentable_type', 255)->nullable()->comment('添付先クラス名');
            $blueprint->string('original_name', 255)->comment('オリジナルファイル名');
            $blueprint->string('path', 255)->comment('保存先パス');
            $blueprint->timestamps();
            $blueprint->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', static function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['user_id']);
        });

        Schema::dropIfExists('attachments');
    }
}
