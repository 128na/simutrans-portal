/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `articlables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articlables` (
  `article_id` bigint unsigned NOT NULL,
  `articlable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `articlable_id` bigint unsigned NOT NULL,
  KEY `articlables_article_id_foreign` (`article_id`),
  KEY `articlables_articlable_type_articlable_id_index` (`articlable_type`,`articlable_id`),
  CONSTRAINT `articlables_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `article_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_category` (
  `article_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  KEY `article_category_article_id_category_id_index` (`article_id`,`category_id`),
  KEY `article_category_category_id_foreign` (`category_id`),
  CONSTRAINT `article_category_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `article_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_tag` (
  `article_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  KEY `article_tag_article_id_tag_id_index` (`article_id`,`tag_id`),
  KEY `article_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `article_tag_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'タイトル',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'スラッグ',
  `post_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '投稿形式',
  `contents` json NOT NULL COMMENT 'コンテンツ',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公開状態',
  `pr` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'PR記事',
  `published_at` timestamp NULL DEFAULT NULL COMMENT '投稿日時',
  `modified_at` timestamp NULL DEFAULT NULL COMMENT '更新日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `articles_user_id_foreign` (`user_id`),
  KEY `articles_published_at_index` (`published_at`),
  KEY `articles_status_published_at_index` (`status`,`published_at`),
  KEY `articles_modified_at_index` (`modified_at`),
  KEY `articles_status_modified_at_index` (`status`,`modified_at`),
  KEY `articles_slug_index` (`slug`),
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `attachmentable_id` bigint unsigned DEFAULT NULL COMMENT '添付先ID',
  `attachmentable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '添付先クラス名',
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'オリジナルファイル名',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '保存先パス',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'キャプション（画像向け）',
  `order` int unsigned NOT NULL DEFAULT '0' COMMENT '表示順（画像向け）',
  PRIMARY KEY (`id`),
  KEY `attachments_user_id_foreign` (`user_id`),
  KEY `attachments_attachmentable_id_attachmentable_type_index` (`attachmentable_id`,`attachmentable_type`),
  CONSTRAINT `attachments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bulk_zips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分類',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'スラッグ',
  `need_admin` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '管理者専用カテゴリ',
  `order` int unsigned NOT NULL DEFAULT '0' COMMENT '表示順',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_type_slug_unique` (`type`,`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `compressed_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compressed_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compressed_images_path_unique` (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `controll_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `controll_options` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `conversion_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversion_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `type` int unsigned NOT NULL COMMENT '集計区分 1:日次,2:月次,3:年次,4:全体',
  `period` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '集計期間',
  `count` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'カウント',
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversion_counts_article_id_type_period_unique` (`article_id`,`type`,`period`),
  CONSTRAINT `conversion_counts_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `file_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_infos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attachment_id` bigint unsigned NOT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_infos_attachment_id_foreign` (`attachment_id`),
  CONSTRAINT `file_infos_attachment_id_foreign` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `login_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_tokens` (
  `application` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`application`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pak_addon_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pak_addon_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pak_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `addon_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `data` json NOT NULL COMMENT 'プロフィール情報',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profiles_user_id_unique` (`user_id`),
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rankings` (
  `rank` int unsigned NOT NULL,
  `article_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`rank`),
  KEY `rankings_article_id_foreign` (`article_id`),
  CONSTRAINT `rankings_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'リダイレクト元',
  `to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'リダイレクト先',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redirects_from_to_unique` (`from`,`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `screenshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `screenshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'タイトル',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '説明',
  `links` json NOT NULL COMMENT 'リンク先一覧',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公開ステータス',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `screenshots_user_id_foreign` (`user_id`),
  KEY `screenshots_status_updated_at_index` (`status`,`updated_at`),
  CONSTRAINT `screenshots_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'タグ名',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '説明',
  `editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:編集可,0:編集不可',
  `created_by` bigint unsigned DEFAULT NULL,
  `last_modified_by` bigint unsigned DEFAULT NULL,
  `last_modified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_created_by_foreign` (`created_by`),
  KEY `tags_last_modified_by_foreign` (`last_modified_by`),
  CONSTRAINT `tags_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tags_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tweet_log_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tweet_log_summaries` (
  `article_id` bigint unsigned NOT NULL,
  `total_retweet_count` bigint unsigned NOT NULL DEFAULT '0',
  `total_reply_count` bigint unsigned NOT NULL DEFAULT '0',
  `total_like_count` bigint unsigned NOT NULL DEFAULT '0',
  `total_quote_count` bigint unsigned NOT NULL DEFAULT '0',
  `total_impression_count` bigint unsigned NOT NULL DEFAULT '0',
  `total_url_link_clicks` bigint unsigned NOT NULL DEFAULT '0',
  `total_user_profile_clicks` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  CONSTRAINT `tweet_log_summaries_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tweet_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tweet_logs` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `article_id` bigint unsigned NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `retweet_count` bigint unsigned NOT NULL DEFAULT '0',
  `reply_count` bigint unsigned NOT NULL DEFAULT '0',
  `like_count` bigint unsigned NOT NULL DEFAULT '0',
  `quote_count` bigint unsigned NOT NULL DEFAULT '0',
  `impression_count` bigint unsigned NOT NULL DEFAULT '0',
  `url_link_clicks` bigint unsigned NOT NULL DEFAULT '0',
  `user_profile_clicks` bigint unsigned NOT NULL DEFAULT '0',
  `tweet_created_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tweet_logs_article_id_foreign` (`article_id`),
  CONSTRAINT `tweet_logs_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_addon_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_addon_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '表示名',
  `count` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_addon_counts_user_id_foreign` (`user_id`),
  CONSTRAINT `user_addon_counts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '権限',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ユーザー名',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '表示名',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `invited_by` bigint unsigned DEFAULT NULL COMMENT '紹介ユーザーID',
  `invitation_code` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '紹介用コード',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_invitation_code_unique` (`invitation_code`),
  UNIQUE KEY `users_nickname_unique` (`nickname`),
  KEY `users_invited_by_index` (`invited_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `view_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `view_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `type` int unsigned NOT NULL COMMENT '集計区分 1:日次,2:月次,3:年次,4:全体',
  `period` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '集計期間',
  `count` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'カウント',
  PRIMARY KEY (`id`),
  UNIQUE KEY `view_counts_article_id_type_period_unique` (`article_id`,`type`,`period`),
  CONSTRAINT `view_counts_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2019_06_15_030422_create_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2019_06_15_030437_create_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2019_06_15_030449_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2019_06_15_030503_create_attachments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2019_06_15_032541_create_article_category_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2019_06_19_044839_create_views_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2019_06_19_044921_create_conversions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2019_06_20_044339_create_tags_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2019_06_20_044500_create_article_tag_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2019_06_21_114119_create_pak_addon_counts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2019_06_21_114141_create_user_addon_counts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2019_06_28_180849_create_view_counts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2019_06_28_212059_create_conversion_counts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2019_07_01_092824_create_redirects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2019_07_21_204425_create_compressed_images_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2020_04_21_093738_add_type_slug_index_to_categories_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2020_04_21_094946_add_attachmentable_id_type_index_to_attachments_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2020_11_06_210330_add_deleted_at_column_in_users_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2020_11_06_210657_add_deleted_at_column_in_articles_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2021_04_07_133308_create_bookmarks_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2021_04_07_135155_create_bookmark_items_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2021_04_18_124101_create_bulk_zips_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2021_04_18_170917_create_failed_jobs_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2021_04_18_171129_create_jobs_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2021_06_26_164614_create_registration_orders_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2021_07_08_183241_create_cache_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2021_07_08_183455_create_sessions_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2016_06_01_000001_create_oauth_auth_codes_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2016_06_01_000002_create_oauth_access_tokens_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2016_06_01_000003_create_oauth_refresh_tokens_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2016_06_01_000004_create_oauth_clients_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2016_06_01_000005_create_oauth_personal_access_clients_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2021_07_17_221321_create_projects_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2021_07_17_231543_create_project_users_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2021_07_18_204917_add_user_id_column_in_projects_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2021_07_20_181905_add_redirect_column_in_projects_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2022_04_24_115923_add_invited_by_column_in_users_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2022_04_30_150538_drop_oauth',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2022_04_30_152218_drop_registration_orders_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2022_04_30_152335_drop_projects_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2022_05_05_114210_drop_bookmark_tables',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2022_06_02_235812_create_tweet_logs_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2022_06_03_000138_create_tweet_log_summaries_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2022_06_05_100944_create_oauth_codes_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2022_06_05_180604_add_column_in_tweet_logs_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2022_06_05_180617_add_column_in_tweet_log_summaries_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2022_06_13_112953_alter_default_value_in_tweet_logs_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2022_06_19_162839_create_rankings_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2022_06_21_222819_create_file_infos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2022_07_02_104126_add_published_at_column_in_articles_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2019_12_14_000001_create_personal_access_tokens_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2022_11_12_005730_add_description_column_in_tags_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2022_11_15_152852_create_controll_options_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2023_03_11_172255_drop_compressed_images_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2023_05_04_152337_create_login_histories_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2023_12_23_000742_remove_slug_unique_in_articles_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2024_01_23_165038_add_nickname_column_in_users_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2024_01_23_170740_add_nickname_column_in_user_addons_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2014_10_12_200000_add_two_factor_columns_to_users_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2024_03_16_172944_create_screenshots_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2024_03_16_225456_create_article_relation_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2024_03_22_155518_add_column_in_attachments_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2024_03_23_172212_drop_category_name_in_categories_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2024_03_31_125117_add_pr_column_in_articles_table',30);
