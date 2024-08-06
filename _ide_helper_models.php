<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title タイトル
 * @property string $slug スラッグ
 * @property \App\Enums\ArticlePostType $post_type 投稿形式
 * @property \App\Models\Contents\Content $contents コンテンツ
 * @property \App\Enums\ArticleStatus $status 公開状態
 * @property bool $pr PR記事
 * @property \Carbon\CarbonImmutable|null $published_at 投稿日時
 * @property \Carbon\CarbonImmutable|null $modified_at 更新日時
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ConversionCount> $conversionCounts
 * @property-read int|null $conversion_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ConversionCount> $dailyConversionCounts
 * @property-read int|null $daily_conversion_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ViewCount> $dailyViewCounts
 * @property-read int|null $daily_view_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $category_addons
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $category_pak128_positions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $category_paks
 * @property-read \App\Models\Attachment|null $file
 * @property-read bool $has_file
 * @property-read bool $has_file_info
 * @property-read bool $has_thumbnail
 * @property-read string $headline_description
 * @property-read bool $is_addon_post
 * @property-read bool $is_inactive
 * @property-read bool $is_page
 * @property-read bool $is_publish
 * @property-read bool $is_reservation
 * @property-read string $meta_description
 * @property-read \App\Models\Attachment|null $thumbnail
 * @property-read string $thumbnail_url
 * @property-read string $todays_conversion_rate
 * @property-read string $url_decoded_slug
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ConversionCount> $monthlyConversionCounts
 * @property-read int|null $monthly_conversion_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ViewCount> $monthlyViewCounts
 * @property-read int|null $monthly_view_counts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Article\Ranking|null $ranking
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Article> $relatedArticles
 * @property-read int|null $related_articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $relatedScreenshots
 * @property-read int|null $related_screenshots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Article\ConversionCount|null $todaysConversionCount
 * @property-read \App\Models\Article\ViewCount|null $todaysViewCount
 * @property-read \App\Models\Article\ConversionCount|null $totalConversionCount
 * @property-read \App\Models\Article\ViewCount|null $totalViewCount
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ViewCount> $viewCounts
 * @property-read int|null $view_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ConversionCount> $yearlyConversionCounts
 * @property-read int|null $yearly_conversion_counts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article\ViewCount> $yearlyViewCounts
 * @property-read int|null $yearly_view_counts_count
 * @method static \Illuminate\Database\Eloquent\Builder|Article active()
 * @method static \Illuminate\Database\Eloquent\Builder|Article addon()
 * @method static \Illuminate\Database\Eloquent\Builder|Article announce()
 * @method static \Illuminate\Database\Eloquent\Builder|Article category(\App\Models\Category $category)
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Article linkCheckTarget()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article page()
 * @method static \Illuminate\Database\Eloquent\Builder|Article pak(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Article pakAddonCategory(\App\Models\Category $pak, \App\Models\Category $addon)
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article rankingOrder()
 * @method static \Illuminate\Database\Eloquent\Builder|Article slug(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Article tag(\App\Models\Tag $tag)
 * @method static \Illuminate\Database\Eloquent\Builder|Article user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Article withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article withUserTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Article withoutAnnounce()
 * @method static \Illuminate\Database\Eloquent\Builder|Article withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperArticle {}
}

namespace App\Models\Article{
/**
 * 
 *
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property-read \App\Models\Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversionCount query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperConversionCount {}
}

namespace App\Models\Article{
/**
 * 
 *
 * @property int $rank
 * @property int $article_id
 * @property-read \App\Models\Article $article
 * @method static \Database\Factories\Article\RankingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ranking query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperRanking {}
}

namespace App\Models\Article{
/**
 * 
 *
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property-read \App\Models\Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperViewCount {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $attachmentable_id 添付先ID
 * @property string|null $attachmentable_type 添付先クラス名
 * @property string $original_name オリジナルファイル名
 * @property string $path 保存先パス
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $caption キャプション（画像向け）
 * @property int $order 表示順（画像向け）
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $attachmentable
 * @property-read \App\Models\Attachment\FileInfo|null $fileInfo
 * @property-read string $extension
 * @property-read string|null $file_contents
 * @property-read string $full_path
 * @property-read bool $is_image
 * @property-read bool $is_png
 * @property-read bool $path_exists
 * @property-read string $thumbnail
 * @property-read string $type
 * @property-read string $url
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\AttachmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperAttachment {}
}

namespace App\Models\Attachment{
/**
 * 
 *
 * @property int $id
 * @property int $attachment_id
 * @property array $data
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperFileInfo {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $uuid
 * @property string $bulk_zippable_type
 * @property int $bulk_zippable_id
 * @property bool $generated ファイル生成済みか 0:未生成,1:生成済み
 * @property string|null $path 生成ファイルのパス
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $bulkZippable
 * @method static \Database\Factories\BulkZipFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperBulkZip {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \App\Enums\CategoryType $type 分類
 * @property string $slug スラッグ
 * @property bool $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category addon()
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Category forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Category license()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category page()
 * @method static \Illuminate\Database\Eloquent\Builder|Category pak()
 * @method static \Illuminate\Database\Eloquent\Builder|Category pak128Position()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category slug(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Category type(\App\Enums\CategoryType $categoryType)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperCategory {}
}

namespace App\Models{
/**
 * 
 *
 * @property \App\Enums\ControllOptionKey $key
 * @property bool $value
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ControllOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ControllOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ControllOption query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperControllOption {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $application
 * @property string $token_type
 * @property string $scope
 * @property string $access_token
 * @property string $refresh_token
 * @property \Carbon\CarbonImmutable $expired_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperOauthToken {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $from リダイレクト元
 * @property string $to リダイレクト先
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\RedirectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Redirect from(string $from)
 * @method static \Illuminate\Database\Eloquent\Builder|Redirect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Redirect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Redirect query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperRedirect {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title タイトル
 * @property string $description 説明
 * @property array $links リンク先一覧
 * @property \App\Enums\ScreenshotStatus $status 公開ステータス
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read bool $is_publish
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ScreenshotFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot publish()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperScreenshot {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name タグ名
 * @property string|null $description 説明
 * @property bool $editable 1:編集可,0:編集不可
 * @property int|null $created_by
 * @property int|null $last_modified_by
 * @property \Carbon\CarbonImmutable|null $last_modified_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastModifiedBy
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag popular()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperTag {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \App\Enums\UserRole $role 権限
 * @property string $name ユーザー名
 * @property string|null $nickname 表示名
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Carbon\CarbonImmutable|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property int|null $invited_by 紹介ユーザーID
 * @property string|null $invitation_code 紹介用コード
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \App\Models\BulkZip|null $bulkZippable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $createdTags
 * @property-read int|null $created_tags_count
 * @property-read User|null $invited
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $invites
 * @property-read int|null $invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $invitesReclusive
 * @property-read int|null $invites_reclusive_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $lastModifiedBy
 * @property-read int|null $last_modified_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\LoginHistory> $loginHistories
 * @property-read int|null $login_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $myAttachments
 * @property-read int|null $my_attachments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\User\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Redirect> $redirects
 * @property-read int|null $redirects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $screenshots
 * @property-read int|null $screenshots_count
 * @method static \Illuminate\Database\Eloquent\Builder|User admin()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperUser {}
}

namespace App\Models\User{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $ip
 * @property string|null $ua
 * @property string|null $referer
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\User\LoginHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperLoginHistory {}
}

namespace App\Models\User{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property \App\Models\User\ProfileData $data プロフィール情報
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\Attachment|null $avatar
 * @property-read string $avatar_url
 * @property-read bool $has_avatar
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperProfile {}
}

