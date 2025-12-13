# Repository パターンのリファクタリング

## 概要

このドキュメントは、Repository パターンの継承を除去し、統一したアーキテクチャに移行したリファクタリングの詳細を記録します。

## 背景

以前は、一部の Repository が `BaseRepository` や `BaseCountRepository` を継承していましたが、以下の問題がありました:

- **継承基準が不明確**: どの Repository が Base を継承すべきか判断できない
- **コードの重複**: 共通メソッドを各 Repository で実装している可能性
- **保守性の低下**: 統一ルールがないため変更が難しい
- **新規開発時の混乱**: どちらのパターンを採用すべきか迷う

## 実装方針

**「オプション2: 継承しないパターンで統一」** を採用しました。

### 理由

- リファクタリングの名残として残っていた BaseRepository の継承を整理
- 各 Repository を独立させ、必要なメソッドのみを実装する
- シンプルで理解しやすいコードベースを維持

## 変更内容

### 1. BaseRepository を継承していた Repository の変更

以下の Repository から継承を除去し、必要なメソッドのみを実装:

#### AttachmentRepository

- 継承を除去
- 実装したメソッド:
  - `find()` - ImageAttachment ルールで使用
  - `syncProfile()` - UpdateProfile アクションで使用

#### OauthTokenRepository

- 継承を除去
- 実装したメソッド:
  - `getToken()` - Twitter API 関連で使用
  - `updateOrCreate()` - PKCE サービスで使用
  - `delete()` - PKCE サービスで使用

#### User/ProfileRepository

- 継承を除去
- 実装したメソッド:
  - `update()` - UpdateProfile アクションで使用
  - `store()`, `find()`, `delete()` - テストでのみ使用

#### Attachment/FileInfoRepository

- 継承を除去
- 実装したメソッド:
  - `updateOrCreate()` - FileInfoService で使用
  - `store()`, `find()`, `update()`, `delete()` - テストでのみ使用

### 2. BaseCountRepository を継承していた Repository の変更

#### Article/ViewCountRepository

- 継承を除去
- `countUp()` メソッドの機能をインライン化
- テーブル名 `view_counts` をハードコード

#### Article/ConversionCountRepository

- 継承を除去
- `countUp()` メソッドの機能をインライン化
- テーブル名 `conversion_counts` をハードコード

### 3. BaseRepository と BaseCountRepository

- 両クラスに `@deprecated` アノテーションを追加
- 将来的な削除のために保持（まだ参照される可能性があるため）

### 4. テストの変更

- `tests/Feature/Repositories/BaseRepositoryTest.php` を削除
  - BaseRepository が使用されなくなったため不要
- 他の Repository のテストはそのまま維持

### 5. ドキュメントの更新

`.github/copilot-instructions.md` に Repository パターンのガイドラインを追加:

- 継承を使用しない基本方針
- 実装パターンの例
- 命名規則
- 推奨するパターン / 推奨しないパターン

## 実装パターン

### 基本構造

```php
class ExampleRepository
{
    public function __construct(private readonly Example $model) {}

    // 必要なメソッドのみを実装
    public function find(int $id): ?Example
    {
        return $this->model->find($id);
    }

    // ドメイン固有のメソッド
    public function findByCondition(string $condition): ?Example
    {
        return $this->model->where('condition', $condition)->first();
    }
}
```

### 命名規則

- **単体取得**: `find()`, `findOrFail()`, `findBy{条件}()`
- **一覧取得**: `getFor{用途}()`, `getBy{条件}()`
- **作成**: `store()`
- **更新**: `update()`
- **削除**: `delete()`
- **関連付け**: `sync{関連名}()`

## 影響範囲

### 変更されたファイル

```
.github/copilot-instructions.md                        | +49
app/Repositories/Article/ConversionCountRepository.php | +37 -1
app/Repositories/Article/ViewCountRepository.php       | +37 -1
app/Repositories/Attachment/FileInfoRepository.php     | +42 -2
app/Repositories/AttachmentRepository.php              | +11 -2
app/Repositories/BaseCountRepository.php               | +3
app/Repositories/BaseRepository.php                    | +2
app/Repositories/OauthTokenRepository.php              | +24 -2
app/Repositories/User/ProfileRepository.php            | +33 -2
tests/Feature/Repositories/BaseRepositoryTest.php      | -294
```

### 影響を受けなかったファイル

以下の Repository は元々継承を使用していなかったため、変更なし:

- ArticleRepository
- UserRepository
- TagRepository
- CategoryRepository
- RedirectRepository
- LoginHistoryRepository

## 期待される効果

- ✅ **一貫性の確保**: すべての Repository で統一されたパターン
- ✅ **保守性の向上**: 変更箇所が明確になる
- ✅ **独立性の向上**: 各 Repository が独立し、継承の制約がない
- ✅ **開発効率の向上**: 新規 Repository 作成時のガイドラインが明確
- ✅ **コードの簡潔性**: 使用しないメソッドを持たない

## 今後の対応

### 新規 Repository の作成

1. BaseRepository や BaseCountRepository を継承しない
2. `private readonly` でモデルを受け取る
3. 実際に使用するメソッドのみを実装
4. `.github/copilot-instructions.md` の命名規則に従う

### BaseRepository と BaseCountRepository の削除

現時点では `@deprecated` マークのみ追加していますが、将来的には以下の手順で削除可能:

1. すべての参照がないことを確認
2. rector.php の設定を更新
3. ファイルを削除

## 参考資料

- [.github/copilot-instructions.md - Repository パターン](../.github/copilot-instructions.md)
- [docs/architecture-services-and-actions.md](./architecture-services-and-actions.md)
