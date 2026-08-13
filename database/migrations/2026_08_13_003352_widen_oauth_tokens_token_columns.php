<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->text('access_token')->change();
            $table->text('refresh_token')->change();
        });
    }

    /**
     * 注意: OauthToken::casts()でencryptedキャストが有効になった後にロールバックすると、
     * 暗号化済みペイロード（通常255文字超）がvarchar(255)に収まらず、データが破損する。
     * ロールバックする場合は、先にencryptedキャストを外し、平文に戻してから実行すること。
     */
    public function down(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->string('access_token')->change();
            $table->string('refresh_token')->change();
        });
    }
};
