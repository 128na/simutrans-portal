<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentableIdTypeIndexToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attachments', static function (Blueprint $table): void {
            $table->index(['attachmentable_id', 'attachmentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', static function (Blueprint $table): void {
            $table->dropIndex(['attachmentable_id', 'attachmentable_type']);
        });
    }
}
