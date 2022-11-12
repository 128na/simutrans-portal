<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnInTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name')->comment('説明');
            $table->boolean('editable')->after('description')->default(true)->comment('1:編集可,0:編集不可');
            $table->unsignedBigInteger('created_by')->nullable()->after('editable');
            $table->unsignedBigInteger('last_modified_by')->nullable()->after('created_by');
            $table->foreign('created_by')->nullOnDelete()->references('id')->on('users');
            $table->foreign('last_modified_by')->nullOnDelete()->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['last_modified_by']);
            $table->dropColumn([
                'description',
                'editable',
                'created_by',
                'last_modified_by',
            ]);
        });
    }
}
