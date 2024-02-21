<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishedAtColumnInArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('status')->comment('投稿日時');
            $table->index(['published_at']);
            $table->index(['status', 'published_at']);

            $table->timestamp('modified_at')->nullable()->after('published_at')->comment('更新日時');
            $table->index(['modified_at']);
            $table->index(['status', 'modified_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
            $table->dropIndex(['status', 'published_at']);
            $table->dropColumn('published_at');

            $table->dropIndex(['modified_at']);
            $table->dropIndex(['status', 'modified_at']);
            $table->dropColumn('modified_at');
        });
    }
}
