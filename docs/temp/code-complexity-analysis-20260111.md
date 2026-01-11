# コード複雑性・責務分離分析（マイリスト機能）

日付：2026-01-11  
対象：マイリスト機能の全コンポーネント・サービス  
ステータス：分析完了

---

## 概要

現在のマイリスト実装（フロント + バック）における100行以上のファイルを調査し、単一責務の原則に基づいて責務分離の必要性をチェックしました。

**全体評価**: ✅ **良好** - ほぼ適切に分離されているが、以下の点で改善推奨あり

---

## 1. フロントエンド（TypeScript / React）

### 調査対象ファイル

| ファイル                                                                        | 行数 | 責務                               | 評価                      |
| ------------------------------------------------------------------------------- | ---- | ---------------------------------- | ------------------------- |
| [AddToMyList.tsx](../../resources/js/features/mylist/AddToMyList.tsx)           | 256  | 記事追加・モーダル・リスト管理     | ⚠️ **リファクタ推奨**     |
| [MyListTable.tsx](../../resources/js/features/mylist/MyListTable.tsx)           | 270  | 一覧表示・CRUD・3つのモーダル      | ⚠️ **大幅リファクタ推奨** |
| [MyListItemsTable.tsx](../../resources/js/features/mylist/MyListItemsTable.tsx) | 260  | アイテム表示・編集・削除・並び替え | ⚠️ **分割推奨**           |

### 詳細分析

#### 📌 AddToMyList.tsx （256行）

**現在の責務**:

1. ✅ 「マイリスト追加」ボタンの表示・状態管理
2. ✅ モーダルの開閉制御
3. ⚠️ リスト一覧取得 API呼び出し
4. ⚠️ リスト新規作成フォーム＆API
5. ⚠️ 複数リストへの一括追加ロジック
6. ⚠️ エラー・ローディング状態管理

**改善提案**:

```
推奨分割構成:
- AddToMyListButton (50行)
  └─ ボタン表示＆モーダル開閉のみ

- AddToMyListModal (206行)
  ├─ useMyListSelection (カスタムHook)
  │  └─ リスト取得・選択状態・追加API呼び出し
  ├─ MyListSelector (60行コンポーネント)
  │  └─ リスト一覧チェックボックス表示
  └─ NewListCreator (80行コンポーネント)
     └─ 新規作成フォーム＆API
```

**現状コード構造**:

```tsx
// ❌ 現在：256行すべて1ファイル
export const AddToMyListButton → AddToMyListModal
  ├─ useState x 6個
  ├─ useEffect（リスト取得）
  ├─ handleToggleList
  ├─ handleCreateList（新規作成API）
  ├─ handleAddToLists（複数追加API）
  └─ renderUI（3セクション）
```

**リファクタ後イメージ**:

```tsx
// ✅ 提案：責務を分割
resources/js/features/mylist/
├─ AddToMyList.tsx (50行)
│  └─ ボタン＆モーダル開閉のみ
├─ components/
│  ├─ MyListSelector.tsx (60行)
│  │  └─ リスト選択UI
│  └─ NewListCreator.tsx (80行)
│     └─ リスト作成フォーム
└─ hooks/
   └─ useMyListSelection.ts (100行)
      └─ リスト取得・API・状態管理
```

**優先度**: 🔴 **高** - 機能が複雑で、今後の拡張性に影響

---

#### 📌 MyListTable.tsx （270行）

**現在の責務**:

1. ✅ マイリスト一覧テーブル表示
2. ⚠️ 公開URLコピー機能
3. ⚠️ 編集モーダル（フォーム＋API）
4. ⚠️ 削除確認モーダル（API）
5. ⚠️ トースト通知管理

**問題点**:

- **3つのモーダルが1ファイルに詰まっている** → 保守性低下
- 編集モーダルが50行、削除確認モーダルが40行の規模
- APIロジックとUIロジックが混在

**改善提案**:

```
推奨分割構成:
resources/js/features/mylist/
├─ MyListTable.tsx (120行)
│  └─ テーブル表示＆コールバック呼び出しのみ
├─ modals/
│  ├─ MyListEditModal.tsx (80行)
│  │  └─ 編集フォーム＆API
│  └─ MyListDeleteModal.tsx (50行)
│     └─ 削除確認＆API
└─ hooks/
   ├─ useMyListEdit.ts (60行)
   │  └─ 編集API＆状態管理
   └─ useMyListDelete.ts (50行)
      └─ 削除API＆状態管理
```

**現状コード構造**:

```tsx
// ❌ 現在：270行、3つのモーダル＋テーブル
export const MyListTable (120行)
export const MyListEditModal (80行)
export const MyListDeleteModal (40行)
```

**優先度**: 🔴 **高** - 将来的な機能追加（共有・共同編集など）を想定すると必須

---

#### 📌 MyListItemsTable.tsx （260行）

**現在の責務**:

1. ✅ アイテム一覧テーブル表示
2. ✅ メモのインライン編集UI
3. ⚠️ メモ保存API呼び出し
4. ⚠️ アイテム削除API呼び出し
5. ⚠️ 並び替え（上下ボタン）API呼び出し
6. ⚠️ エラー・ローディング状態管理
7. ⚠️ 記事公開状態の判定ロジック

**改善提案**:

```
推奨分割構成:
- MyListItemsTable (140行)
  └─ テーブル表示＆コールバック

- MyListItemRow (80行)
  ├─ 1行の表示ロジック
  └─ インライン編集UI

- useMyListItemOperations (80行)
  ├─ メモ保存
  ├─ アイテム削除
  └─ 並び替えAPI
```

**現状コード構造**:

```tsx
// ❌ 現在：260行すべて1コンポーネント
export const MyListItemsTable
  ├─ useState x 4個（editingItemId, editingNote, isLoading, error）
  ├─ handleEditNote
  ├─ handleSaveNote（API）
  ├─ handleCancelEdit
  ├─ handleDelete（API）
  ├─ handleMoveUp（API）
  ├─ handleMoveDown（API）
  └─ render（map＆複雑なJSX）
```

**優先度**: 🟡 **中** - 今後のメモ検索・フィルタ機能を考えると推奨

---

## 2. バックエンド（PHP / Laravel）

### 調査対象ファイル

| ファイル                                                                       | 行数 | 責務                        | 評価        |
| ------------------------------------------------------------------------------ | ---- | --------------------------- | ----------- |
| [MyListService.php](../../app/Services/MyListService.php)                      | 224  | CRUD・API・ビジネスロジック | ✅ **良好** |
| [MyListController.php](../../app/Http/Controllers/Mypage/MyListController.php) | 194  | 11エンドポイント            | ✅ **良好** |

### 詳細分析

#### ✅ MyListService.php （224行）

**責務**:

1. ✅ リスト CRUD（作成・更新・削除）
2. ✅ アイテム CRUD
3. ✅ 並び替え処理
4. ✅ 公開フィルタリング
5. ✅ slug生成ロジック

**評価**: **良好** - Repository パターンで適切に責務分離されている

```php
// 現在の構成：適切に分離済み
public function getListsForUser()      // リスト一覧取得
public function createList()           // リスト作成
public function updateList()           // リスト更新
public function deleteList()           // リスト削除
public function getItemsForList()      // アイテム一覧取得
public function getPublicItemsForList() // 公開アイテム取得
public function addItem()              // アイテム追加
public function updateItem()           // アイテム更新
public function removeItem()           // アイテム削除
public function reorderItems()         // 並び替え
public function getPublicListBySlug()  // 公開リスト取得
```

**改善余地**:

- ✅ リセット分離が適切（Repository に委譲）
- ✅ 各メソッドは単一責務
- ⚠️ slug生成ロジック（UUID） は将来的に意味のあるスラグに変更する際を想定するなら、専用メソッド化推奨

---

#### ✅ MyListController.php （194行）

**責備**:

1. ✅ 11エンドポイントの処理
2. ✅ リクエスト検証（Form Request で外部化）
3. ✅ 権限チェック（authorize で外部化）
4. ✅ レスポンス形式（Resource で外部化）

**評価**: **良好** - エンドポイント1つあたり平均18行。適切な粒度

```php
// 現在の構成：責務分離が適切
public function index()        // リスト一覧
public function store()        // リスト作成
public function update()       // リスト更新
public function destroy()      // リスト削除
public function getItems()     // アイテム一覧
public function storeItem()    // アイテム追加
public function updateItem()   // アイテム更新
public function destroyItem()  // アイテム削除
public function reorderItems() // 並び替え
public function showPublic()   // 公開リスト表示
```

**改善余地**:

- ✅ 現状で改善不要
- 将来：複数エンドポイント数が増えた場合は Controller 分割検討

---

## 3. 全プロジェクトの100行以上ファイル一覧（参考）

### フロントエンド TOP 10

| ファイル                 | 行数    | 備考                                |
| ------------------------ | ------- | ----------------------------------- |
| PakGenericMetadata.tsx   | 470+    | pkg metadata UI（複雑だが規約準拠） |
| FileInfo.ts              | 400+    | 型定義（許容）                      |
| PlaygroundPage.tsx       | 350+    | テストページ（許容）                |
| **MyListTable.tsx**      | **270** | **⚠️ リファクタ推奨**               |
| ProfileForm.tsx          | 260+    | ユーザープロフィールフォーム        |
| **MyListItemsTable.tsx** | **260** | **⚠️ 分割推奨**                     |
| Article.ts               | 240+    | 型定義（許容）                      |
| pakConstants.ts          | 220+    | 定数（許容）                        |
| AddonPost.tsx            | 210+    | アドオン投稿ページ                  |
| **AddToMyList.tsx**      | **256** | **⚠️ リファクタ推奨**               |

### バックエンド TOP 10（マイリスト関連）

| ファイル                 | 行数 | 責備                 | 評価    |
| ------------------------ | ---- | -------------------- | ------- |
| ArticleRepository.php    | 600+ | 記事Repository       | ✅ 許容 |
| MyListService.php        | 224  | マイリストService    | ✅ 良好 |
| MyListController.php     | 194  | マイリストController | ✅ 良好 |
| MyListItemRepository.php | 100+ | アイテムRepository   | ✅ 良好 |

---

## 4. 改善提案まとめ

### 🔴 優先度：高（即座に対応推奨）

#### フロント側

1. **MyListTable.tsx の分割**

   ```
   現: 270行（テーブル + 3モーダル）
   ↓
   案: テーブル120行 + 編集モーダル + 削除モーダル + Hooks

   理由: モーダルが独立した責務で、将来的な機能追加（共有設定など）に対応しやすくなる
   ```

2. **AddToMyList.tsx の分割**

   ```
   現: 256行（ボタン + モーダル + リスト管理 + 新規作成）
   ↓
   案: ボタン50行 + モーダル + カスタムHook + 子コンポーネント

   理由: リスト選択・新規作成・API呼び出しが密結合。カスタムHook化で再利用性向上
   ```

### 🟡 優先度：中（次のリリースで対応推奨）

1. **MyListItemsTable.tsx の分割**

   ```
   現: 260行（テーブル + 複数のAPI処理）
   ↓
   案: テーブル140行 + アイテム行コンポーネント + カスタムHook

   理由: インライン編集・削除・並び替えの責務を分離。テスト書きやすくなる
   ```

### ✅ 優先度：低（現状で問題なし）

- バックエンド（PHP）: 現状で責務分離が適切。改善不要

---

## 5. リファクタ実施ロードマップ

### Phase 1（直近1-2週間）

```
1. MyListTable.tsx の分割
   ├─ MyListTable.tsx (120行) - テーブル表示のみ
   ├─ modals/MyListEditModal.tsx (80行)
   ├─ modals/MyListDeleteModal.tsx (50行)
   ├─ hooks/useMyListEdit.ts (60行)
   └─ hooks/useMyListDelete.ts (50行)

   影響範囲: 大（インポート箇所更新）
   テスト: 既存 MyListTable.test.tsx を分割・追加
   所要時間: 2-3時間
```

### Phase 2（2-3週間後）

```
2. AddToMyList.tsx の分割
   ├─ AddToMyList.tsx (50行) - ボタンのみ
   ├─ modals/AddToMyListModal.tsx (180行)
   ├─ hooks/useMyListSelection.ts (100行)
   ├─ components/MyListSelector.tsx (60行)
   └─ components/NewListCreator.tsx (80行)

   影響範囲: 中（呼び出し箇所少ない）
   テスト: 既存テスト + 新規テスト分割
   所要時間: 3-4時間
```

### Phase 3（4週間以降）

```
3. MyListItemsTable.tsx の分割（テスト後の様子見）
   ├─ MyListItemsTable.tsx (140行)
   ├─ components/MyListItemRow.tsx (80行)
   └─ hooks/useMyListItemOperations.ts (80行)

   影響範囲: 小（詳細ページのみ使用）
   テスト: 新規作成
   所要時間: 2-3時間
```

---

## 6. 実装チェックリスト

### 作業前確認

- [ ] 既存テストが全てPASS していることを確認
- [ ] `npm run check` を実行してエラーがないことを確認
- [ ] git ブランチを作成（`feature/refactor-mylist-components`）

### 作業中

- [ ] 1つの責務ごとにコンポーネント/Hook を作成
- [ ] TSの型安全性を確認（`tsc` 実行）
- [ ] ESLint が通る （`npm run check`）
- [ ] 既存インポートを全て更新

### 作業後確認

- [ ] 既存テストが全てPASS
- [ ] 新しい機能テストを追加
- [ ] `npm run check` が全てPASS
- [ ] UI動作確認（ブラウザで実際に操作）

---

## 参考資料

- 規約: [project-coding-standards](../knowledge/project-coding-standards-20260101-knowledge.md)
  - 1ファイル1コンポーネント、100～200行推奨
  - React コンポーネントは関数形式 + Hooks
- 作業フロー: [copilot-instructions.md](../../.github/copilot-instructions.md)

---

## 結論

**全体評価**: 現状のマイリスト実装は適切に実装されていますが、フロント側の複合的な責務を持つコンポーネント（特に MyListTable.tsx と AddToMyList.tsx）を分割することで、以下の利点が得られます：

1. **保守性向上**: 各コンポーネントが単一責務に
2. **テスト性向上**: 子コンポーネント単位でのテストが容易
3. **再利用性向上**: カスタムHook化で他ページでも流用可能
4. **拡張性向上**: 新機能追加時の影響範囲を最小化

バックエンド（PHP）は現状で問題ありません。
