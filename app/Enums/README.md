# Enums ディレクトリ

型安全な列挙型（Enum）を配置します。

## 基本原則

- **PHP 8.1+ Enum**: `enum` キーワードを使用
- **Backed Enum**: 文字列または整数値に対応
- **型安全**: 無効な値を防ぐ
- **IDE補完**: エディタで値が補完される

## Enum一覧

### ArticleStatus（記事ステータス）

**ファイル**: `ArticleStatus.php`

```php
enum ArticleStatus: string
{
    case Draft = 'draft';      // 下書き
    case Publish = 'publish';  // 公開
    case Private = 'private';  // 非公開
}
```

**用途**: 記事の公開状態を管理

- `Draft`: 執筆中、未公開
- `Publish`: 公開済み
- `Private`: 非公開（URLアクセスで閲覧可能）

### ArticlePostType（記事投稿タイプ）

**ファイル**: `ArticlePostType.php`

```php
enum ArticlePostType: string
{
    case AddonIntroduction = 'addon-introduction';  // アドオン紹介
    case AddonPost = 'addon-post';                  // アドオン投稿
    case Markdown = 'markdown';                     // Markdown
    case Page = 'page';                             // 固定ページ
}
```

**用途**: 記事のコンテンツ形式を区別

- `AddonIntroduction`: pak128版サイトからの移行記事
- `AddonPost`: 新規アドオン投稿
- `Markdown`: Markdownテキスト記事
- `Page`: 静的ページ（利用規約、プライバシーポリシー等）

### CategoryType（カテゴリタイプ）

**ファイル**: `CategoryType.php`

```php
enum CategoryType: string
{
    case Pak = 'pak';                  // Pakセット
    case AddonType = 'addon-type';     // アドオン種別
    case License = 'license';          // ライセンス
}
```

**用途**: カテゴリの種類を区別

- `Pak`: Pak64, Pak128, Pak256等
- `AddonType`: 車両、建物、地形等
- `License`: Artistic License, GPL等

### UserRole（ユーザーロール）

**ファイル**: `UserRole.php`

```php
enum UserRole: string
{
    case User = 'user';    // 一般ユーザー
    case Admin = 'admin';  // 管理者
}
```

**用途**: ユーザーの権限レベル

- `User`: 自分の記事のみ編集可能
- `Admin`: 全機能アクセス可能

### ArticleAnalyticsType（アナリティクスタイプ）

**ファイル**: `ArticleAnalyticsType.php`

```php
enum ArticleAnalyticsType: string
{
    case View = 'view';              // 閲覧数
    case Conversion = 'conversion';  // ダウンロード数
}
```

**用途**: アクセス解析の種類

- `View`: ページビュー数
- `Conversion`: 添付ファイルダウンロード数

### ControllOptionKey（制御オプションキー）

**ファイル**: `ControllOptionKey.php`

```php
enum ControllOptionKey: string
{
    case RegistrationInvite = 'registration.invite';  // 招待制
    case DiscordWebhook = 'discord.webhook';          // Discord通知
    // ... 他の設定項目
}
```

**用途**: システム設定のキー

- `RegistrationInvite`: 新規登録を招待制にするか
- `DiscordWebhook`: Discord Webhook URL

### ImageFormat（画像フォーマット）

**ファイル**: `ImageFormat.php`

```php
enum ImageFormat: string
{
    case Webp = 'webp';
    case Jpeg = 'jpeg';
    case Png = 'png';
}
```

**用途**: 画像変換フォーマット

- `Webp`: 次世代フォーマット（推奨）
- `Jpeg`: 互換性重視
- `Png`: 透過対応

## 実装パターン

### Backed Enum

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Publish = 'publish';
    case Private = 'private';

    /**
     * ラベルを取得
     */
    public function label(): string
    {
        return match($this) {
            self::Draft => '下書き',
            self::Publish => '公開',
            self::Private => '非公開',
        };
    }

    /**
     * 公開状態かどうか
     */
    public function isPublic(): bool
    {
        return $this === self::Publish;
    }
}
```

### Modelでの使用

```php
use App\Enums\ArticleStatus;

class Article extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
        ];
    }
}

// 使用例
$article->status = ArticleStatus::Publish;

if ($article->status === ArticleStatus::Draft) {
    // 下書き
}

echo $article->status->label();  // "公開"
```

### バリデーション

```php
use Illuminate\Validation\Rules\Enum;

$request->validate([
    'status' => ['required', new Enum(ArticleStatus::class)],
]);

// またはRule::enum()を使用
use Illuminate\Validation\Rule;

$request->validate([
    'status' => ['required', Rule::enum(ArticleStatus::class)],
]);
```

### 全ケースの取得

```php
// すべてのステータスを取得
$statuses = ArticleStatus::cases();

// 値の配列を取得
$values = array_column(ArticleStatus::cases(), 'value');
// ['draft', 'publish', 'private']

// ラベルの配列を取得
$labels = array_map(
    fn($case) => $case->label(),
    ArticleStatus::cases()
);
// ['下書き', '公開', '非公開']
```

### match式との組み合わせ

```php
$message = match($article->status) {
    ArticleStatus::Draft => '下書きとして保存されました',
    ArticleStatus::Publish => '記事を公開しました',
    ArticleStatus::Private => '非公開に設定しました',
};

// デフォルト値付き
$color = match($article->status) {
    ArticleStatus::Draft => 'gray',
    ArticleStatus::Publish => 'green',
    default => 'red',
};
```

## メソッド追加例

### ラベル取得

```php
public function label(): string
{
    return match($this) {
        self::Draft => '下書き',
        self::Publish => '公開',
        self::Private => '非公開',
    };
}
```

### CSSクラス取得

```php
public function cssClass(): string
{
    return match($this) {
        self::Draft => 'badge-secondary',
        self::Publish => 'badge-success',
        self::Private => 'badge-warning',
    };
}
```

### 説明文取得

```php
public function description(): string
{
    return match($this) {
        self::Draft => '執筆中の記事です',
        self::Publish => '公開されています',
        self::Private => 'URLで直接アクセス可能です',
    };
}
```

### 静的コンストラクタ

```php
/**
 * 文字列から生成（見つからなければnull）
 */
public static function tryFromName(string $name): ?self
{
    return array_filter(
        self::cases(),
        fn($case) => $case->name === $name
    )[0] ?? null;
}
```

## テスト

```php
use App\Enums\ArticleStatus;

final class ArticleStatusTest extends TestCase
{
    public function test_label_returns_japanese(): void
    {
        $this->assertEquals('公開', ArticleStatus::Publish->label());
    }

    public function test_is_public_returns_true_for_publish(): void
    {
        $this->assertTrue(ArticleStatus::Publish->isPublic());
        $this->assertFalse(ArticleStatus::Draft->isPublic());
    }

    public function test_from_creates_enum_from_value(): void
    {
        $status = ArticleStatus::from('publish');

        $this->assertEquals(ArticleStatus::Publish, $status);
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $status = ArticleStatus::tryFrom('invalid');

        $this->assertNull($status);
    }
}
```

## 移行ガイド（定数からEnum）

### 従来の定数

```php
// ❌ 旧パターン
class ArticleStatus
{
    const DRAFT = 'draft';
    const PUBLISH = 'publish';
    const PRIVATE = 'private';
}

// 型安全ではない
$article->status = 'invalid-value';  // エラーにならない
```

### Enumに移行

```php
// ✅ 新パターン
enum ArticleStatus: string
{
    case Draft = 'draft';
    case Publish = 'publish';
    case Private = 'private';
}

// 型安全
$article->status = ArticleStatus::Publish;
$article->status = 'invalid-value';  // 型エラー
```

## 関連ドキュメント

- **[Models](../Models/README.md)** - Modelでの使用方法
- **[README.md](../../README.md)** - プロジェクト概要
