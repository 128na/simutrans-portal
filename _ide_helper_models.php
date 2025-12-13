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
 * @property int $id
 * @property int $article_id
 * @property int $failed_count
 * @property \Carbon\CarbonImmutable $last_checked_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Article $article
 * @method static \Database\Factories\ArticleLinkCheckHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleLinkCheckHistory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperArticleLinkCheckHistory {}
}

namespace App\Models\Article{
/**
 * @property int $article_id
 * @property string|null $text
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Article|null $article
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleSearchIndex query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperArticleSearchIndex {}
}

namespace App\Models\Article{
/**
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property int $user_id
 * @property-read \App\Models\Article|null $article
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversionCount query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperConversionCount {}
}

namespace App\Models\Article{
/**
 * @property int $id
 * @property int $article_id
 * @property int $type 集計区分 1:日次,2:月次,3:年次,4:全体
 * @property string $period 集計期間
 * @property int $count カウント
 * @property int $user_id
 * @property-read \App\Models\Article|null $article
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewCount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewCount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewCount query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperViewCount {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $attachmentable_id 添付先ID
 * @property string|null $attachmentable_type 添付先クラス名
 * @property string $original_name オリジナルファイル名
 * @property string $path 保存先パス
 * @property string|null $thumbnail_path
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $caption キャプション（画像向け）
 * @property int $order 表示順（画像向け）
 * @property int $size ファイルサイズ(byte)
 * @property-read \Illuminate\Database\Eloquent\Model|null $attachmentable
 * @property-read \App\Models\Attachment\FileInfo|null $fileInfo
 * @property-read mixed $full_path
 * @property-read bool $is_image
 * @property-read mixed $original
 * @property-read mixed $thumbnail
 * @property-read string $type
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\AttachmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attachment query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAttachment {}
}

namespace App\Models\Attachment{
/**
 * @property int $id
 * @property int $attachment_id
 * @property array<array-key, mixed> $data
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFileInfo {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\CategoryType $type 分類
 * @property string $slug スラッグ
 * @property bool $need_admin 管理者専用カテゴリ
 * @property int $order 表示順
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category order()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category page()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category pak()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category slug(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category type(\App\Enums\CategoryType $categoryType)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCategory {}
}

namespace App\Models{
/**
 * @property \App\Enums\ControllOptionKey $key
 * @property bool $value
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperControllOption {}
}

namespace App\Models{
/**
 * @property string $application
 * @property string $token_type
 * @property string $scope
 * @property string $access_token
 * @property string $refresh_token
 * @property \Carbon\CarbonImmutable $expired_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOauthToken {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $from リダイレクト元
 * @property string $to リダイレクト先
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\RedirectFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirect from(string $from)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Redirect query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRedirect {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
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
 * @property-read User|null $invited
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $invites
 * @property-read int|null $invites_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admin()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models\User{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $ip
 * @property string|null $ua
 * @property string|null $referer
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\User\LoginHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoginHistory {}
}

namespace App\Models\User{
/**
 * @property int $id
 * @property int $user_id
 * @property \App\Models\User\ProfileData $data プロフィール情報
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read mixed $avatar
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProfile {}
}

