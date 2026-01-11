# コード重複パターン分析

キーワード: コード重複, リファクタリング, 設計パターン, 共通化, Hook, Trait
最終更新日：2026-01-11
ステータス：完了

## 概要

フロントエンド（React/TypeScript）とバックエンド（PHP/Laravel）全体にわたるコード重複パターンを分析しました。段階的な実装により、共通化の機会を見落とし、複数箇所で同じパターンが繰り返されています。

---

## 1. フロントエンド（React/TypeScript）の重複パターン

### 📌 パターン1：CRUD モーダル（Edit/Create/Delete）

**課題**:

```tsx
// ❌ TagModal.tsx
const handleSave = async () => {
  const res = tag.id
    ? await axios.post(`/api/v2/tags/${tag.id}`, { description })
    : await axios.post(`/api/v2/tags`, { name, description });
  if ((res.status === 200 || res.status === 201) && onSave) {
    onSave(res.data.data);
  }
};

// ❌ MyListTable.tsx (同じパターン)
const handleSubmit = async (e: React.FormEvent) => {
  const method = list ? "PATCH" : "POST";
  const url = list ? `/api/v1/mylist/${list.id}` : "/api/v1/mylist";
  await axios({ method, url, data: { ... } });
};
```

**解決方法**: `useModelModal` Hook で共通化（実装完了）

```tsx
// ✅ hooks/useModelModal.ts
const { isLoading, error, handleSave } = useModelModal();

const onSubmit = async (e: React.FormEvent) => {
  e.preventDefault();
  await handleSave(() => axios.post(`/api/tags`, { name, description }), {
    successMessage: "保存しました",
    onSuccess: () => onSave(),
  });
};
```

---

### 📌 パターン2：削除確認ダイアログ

**課題**: 削除確認のUI/APIロジックが複数の場所に散在

```tsx
// ❌ window.confirm と Modal の混在
if (!window.confirm("削除しますか？")) return;
await axios.delete(`/api/v1/mylist/${list.id}`);
```

**解決方法**: 統一された削除確認Hook

```tsx
// ✅ 推奨パターン
const handleDelete = async () => {
  if (!confirm("本当に削除しますか？")) return;
  await handleSave(() => axios.delete(`/api/v1/mylist/${list.id}`), {
    successMessage: "削除しました",
    onSuccess: () => onSuccess(),
  });
};
```

---

### 📌 パターン3：フォーム入力フィールド + エラー表示

**課題**: フィールド + ラベル + エラー表示が毎回繰り返される

```tsx
// ❌ 繰り返される実装
<div>
  <FormCaption>名前</FormCaption>
  <TextError>{getError("name")?.join("\n")}</TextError>
  <Input value={name} onChange={(e) => setName(e.target.value)} />
</div>
```

**解決方法**: FormField コンポーネント（Priority 2で計画）

```tsx
// ✅ 提案実装
<FormField label="名前" error={getError("name")} required>
  <Input value={name} onChange={(e) => setName(e.target.value)} />
</FormField>
```

---

### 📌 パターン4：API エラーハンドリング + トースト通知

**課題**: エラーハンドリング方式が3パターン混在

```tsx
// ❌ 異なるパターン
try {
  const { data } = await axios.get("/api/v1/mylist");
  setError(err instanceof Error ? err.message : "エラーが発生しました");
} catch (err) {
  setError(extractErrorMessage(err));
}
```

**解決方法**: `useApiCall` Hook で統一（✅ 実装完了）

```tsx
// ✅ useApiCall.ts
const { call, isLoading } = useApiCall();
await call(() => axios.post("/api/tags", data), {
  successMessage: "保存しました",
  onSuccess: () => refresh(),
});
```

---

### 📌 パターン5：ページ初期化（Blade 埋め込みデータ読み込み）

**課題**: 各ページで同じ Blade 読み込みパターンを繰り返し

```tsx
// ❌ JSON.parse エラー処理がない
const user = JSON.parse(
  document.getElementById("data-user")?.textContent || "{}"
);
```

**解決方法**: ページ初期化ユーティリティ（計画中）

```tsx
// ✅ utils/bootpage.ts
const user = getBootData("user", {} as User.MypageShow);
const articles = getBootData("articles", [] as Article[]);
```

---

## 2. バックエンド（PHP / Laravel）の重複パターン

### 📌 パターン1：Repository の create/update/delete

**課題**: BaseRepository が deprecated で、各Repository が個別実装

```php
// ❌ MyListRepository, TagRepository, UserRepository で同じ実装
public function create(array $data): Model { return $this->model->create($data); }
public function update(Model $model, array $data): void { $model->update($data); }
public function delete(Model $model): void { $model->delete(); }
```

**解決方法**: HasCrud Trait（✅ 実装完了）

```php
// ✅ app/Repositories/Concerns/HasCrud.php
trait HasCrud {
    public function create(array $data): Model { return $this->model->create($data); }
    public function update(Model $model, array $data): void { $model->update($data); }
    public function delete(Model $model): void { $model->delete(); }
}

// 使用例
class MyListRepository {
    use HasCrud;  // CRUD操作を継承
    // リポジトリ固有メソッドのみ実装
}
```

---

### 📌 パターン2：Controller の CRUD テンプレート

**課題**: index/store/update/destroy が各Controller で繰り返される

```php
// ❌ 各Controller で同じパターン
public function index(Request $request) {
    $page = (int) $request->query('page', 1);
    $perPage = (int) $request->query('per_page', 20);
    $items = $this->service->getItems($page, $perPage);
    return ItemResource::collection($items);
}
```

**解決方法**: HasIndexAction Trait（計画中）

```php
// ✅ 提案パターン
trait HasIndexAction {
    abstract protected function getService();
    abstract protected function getResourceClass();

    public function index(Request $request) {
        $page = (int) $request->query('page', 1);
        $items = $this->getService()->getItems($page);
        return $this->getResourceClass()::collection($items);
    }
}
```

---

### 📌 パターン3：API レスポンス形式

**課題**: `JsonResponse` vs `Resource` の使い分けが統一されていない

```php
// ❌ 異なるレスポンス形式
public function store(): JsonResponse { return (new MyListResource($list))->response()->setStatusCode(201); }
public function destroy(): JsonResponse { return response()->json(status: 200); }
public function update(): TagResource { return new TagResource($tag); }
```

**解決方法**: レスポンス統一ヘルパー（計画中）

```php
// ✅ Http/Responses/ApiResponse.php
class ApiResponse {
    public static function created(Model $model): JsonResponse { /* ... */ }
    public static function updated(Model $model): JsonResponse { /* ... */ }
    public static function deleted(): JsonResponse { /* ... */ }
}
```

---

### 📌 パターン4：バリデーション ルール

**課題**: ルール定義が散在、一元管理されていない

```php
// ❌ 各Requestクラスでルールをハードコード
public function rules(): array {
    return ['name' => 'required|string|min:1|max:20|unique:tags,name'];
}
```

**解決方法**: バリデーション ルール定数化（計画中）

```php
// ✅ App/Rules/ValidationRules.php
class ValidationRules {
    const TITLE_RULES = 'required|string|min:1|max:120';
    const TAG_NAME_RULES = 'required|string|min:1|max:20|unique:tags,name';
}
```

---

## 3. 全体のコード重複の集計

### フロントエンド側

| パターン               | 重複箇所 | 削減可能行数 | 優先度 | 状態      |
| ---------------------- | -------- | ------------ | ------ | --------- |
| CRUD モーダル          | 5+       | 80-100       | 🔴 高  | ✅ 実装中 |
| 削除ロジック           | 3+       | 40-50        | 🟡 中  | ⬜ 計画中 |
| フォーム入力フィールド | 10+      | 100-150      | 🟡 中  | ⬜ 計画中 |
| APIエラーハンドリング  | 8+       | 120-160      | 🔴 高  | ✅ 完了   |
| ページ初期化           | 5+       | 50-75        | 🟡 中  | ⬜ 計画中 |
| **合計**               | **31+**  | **390-535**  | -      | -         |

### バックエンド側

| パターン        | 重複箇所 | 削減可能行数 | 優先度 | 状態      |
| --------------- | -------- | ------------ | ------ | --------- |
| CRUD Repository | 8+       | 50-70        | 🟡 中  | ✅ 完了   |
| CRUD Controller | 4+       | 40-60        | 🟡 中  | ⬜ 計画中 |
| レスポンス形式  | 6+       | 20-30        | 🟡 中  | ⬜ 計画中 |
| バリデーション  | 15+      | 30-50        | 🟢 低  | ⬜ 計画中 |
| **合計**        | **33+**  | **140-210**  | -      | -         |

---

## 4. 設計パターン：実装済みの知見

### useModelModal Hook の型設計

**課題**: `getError()` の戻り値が `string | string[] | null` になるため、JSX での使用時に型チェックが必要

**解決策**: IIFE + Array.isArray() で型ガード

```tsx
// ✅ 型安全な実装
<TextError>
  {(() => {
    const error = getError("fieldName");
    if (Array.isArray(error)) {
      return error.join("\n");
    }
    return error || undefined;
  })()}
</TextError>
```

### モーダル統合のステップ

1. Hook から必要な値を取り出す
2. フォームの状態管理：既存の `useState` を活用
3. API 呼び出し：`handleSave()` でラップ
4. エラー表示：`error` を UI に反映

### パフォーマンス考慮

- useModelModal Hook は軽量（状態管理 + エラーハンドリング）
- 複数モーダルでも問題なし（独立した Hook インスタンス）
- API 呼び出し時のローディング状態は自動管理

---

## 5. 段階的実装による今後の対策

### なぜ重複が生じたか

1. **新機能を並行開発**
   - マイリスト機能開発中に他の機能も進行
   - 既存パターンの確認が十分でなかった

2. **チーム拡大に伴う実装パターンのばらつき**
   - 複数の開発者が独立して実装
   - コードレビュー時に共通化の指摘が後付け

3. **プロトタイプ → 本実装の段階での改善漏れ**
   - 動作確認優先で共通化が後回し

### 今後の予防策

1. **実装前のコードレビュー**
   - 新機能の PR 前に existing patterns をチェック
   - 相似したコンポーネント/関数が既にないか確認

2. **定期的なコード監査**
   - 月1回の複雑度分析
   - 重複パターンの早期発見

3. **開発ガイドラインの強化**
   - マイリスト機能の実装パターンを docs に記載
   - 次の機能でテンプレートとして利用

---

## 参考：設計パターン例

### useApiCall Hook の使用パターン

```tsx
// ✅ useApiCall を使用した標準的なAPI呼び出し
const { call, isLoading } = useApiCall();

const handleCreate = async () => {
  await call(() => axios.post("/api/tags", { name, description }), {
    successMessage: "タグを作成しました",
    onSuccess: (result) => {
      setTags([...tags, result]);
    },
  });
};
```

### HasCrud Trait の使用パターン

```php
// ✅ HasCrud を使用したRepository実装
class TagRepository {
    use HasCrud;

    // CRUD操作は継承
    // リポジトリ固有メソッドのみ記述
    public function findBySlug(string $slug): ?Tag {
        return $this->model->where('slug', $slug)->first();
    }
}
```
