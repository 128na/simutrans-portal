<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_orders', static function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('twitter')->comment('Twitterユーザー名');
            $table->string('name')->comment('ユーザー名');
            $table->string('code')->nullable()->comment('招待コード');
            $table->text('request_info')->comment('リクエスト情報');
            $table->string('status')->default('processing')->comment('処理状態(processing,rejected,approval)');
            $table->string('rejected_reason')->nullable()->comment('却下理由');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_orders');
    }
}
