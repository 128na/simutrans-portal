<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('bulk_zips');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        CREATE TABLE `bulk_zips` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `bulk_zippable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        `bulk_zippable_id` bigint unsigned NOT NULL,
        `generated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ファイル生成済みか 0:未生成,1:生成済み',
        `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '生成ファイルのパス',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `bulk_zips_uuid_unique` (`uuid`),
        KEY `bulk_zips_bulk_zippable_type_bulk_zippable_id_index` (`bulk_zippable_type`,`bulk_zippable_id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        */
        Schema::create('bulk_zips', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->uuid('uuid')->unique();
            $blueprint->morphs('bulk_zippable');
            $blueprint->boolean('generated')->default(false)->comment('ファイル生成済みか 0:未生成,1:生成済み');
            $blueprint->string('path')->nullable()->comment('生成ファイルのパス');
            $blueprint->timestamps();
        });
    }
};
