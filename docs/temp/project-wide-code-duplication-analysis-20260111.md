# プロジェクト全体コード重複・共通化分析

日付：2026-01-11  
対象：フロントエンド + バックエンド全体  
ステータス：分析完了

---

## 概要

段階的な実装による重複コード、未共通化パターン、責務重複を洗い出しました。

**全体評価**: ⚠️ **改善の余地あり** - 段階的な実装による以下の課題を発見

---

## 1. フロントエンド（React/TypeScript）の重複パターン

### 📌 パターン1：CRUD モーダル（Edit/Create/Delete）

**現状**:

| コンポーネント    | 行数 | パターン                     | 共通化度    |
| ----------------- | ---- | ---------------------------- | ----------- |
| TagModal.tsx      | 85   | Edit/Create フォーム         | 🟡 部分的   |
| MyListEditModal   | 80   | Edit/Create フォーム         | 🟡 部分的   |
| MyListDeleteModal | 50   | Delete確認                   | ❌ 未共通化 |
| ArticleModal.tsx  | 60   | メニュー型（Edit/Show/Copy） | ❌ 未共通化 |

**問題点**:

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

  await axios({
    method,
    url,
    data: { ... },
  });
};
```

**改善提案**:

カスタムHookで共通化

```tsx
// ✅ 推奨：hooks/useModelModal.ts
export const useModelModal = <T extends { id?: number }>(
  endpoint: string,
  onSave?: (item: T) => void
) => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const save = async (data: Omit<T, "id">) => {
    try {
      setIsLoading(true);
      const res = data.id
        ? await axios.patch(`${endpoint}/${data.id}`, data)
        : await axios.post(endpoint, data);
      onSave?.(res.data.data);
    } catch (err) {
      setError(extractErrorMessage(err));
    } finally {
      setIsLoading(false);
    }
  };

  return { save, isLoading, error };
};
```

**使用例**:

```tsx
// TagModal.tsx
const { save, isLoading, error } = useModelModal("/api/v2/tags", onSave);

// MyListEditModal.tsx
const { save, isLoading, error } = useModelModal(`/api/v1/mylist`, onSave);
```

**優先度**: 🔴 高  
**効果**: 30-40行の重複コード削減 × 複数コンポーネント

---

### 📌 パターン2：削除確認ダイアログ

**現状**:

```tsx
// MyListDeleteModal.tsx
const handleDelete = async () => {
  if (!list) return;
  try {
    await axios.delete(`/api/v1/mylist/${list.id}`);
    onSuccess();
  } catch (err) {
    setError(extractErrorMessage(err));
  }
};

// SectionForm.tsx
const remove = (index: number) => {
  if (!window.confirm("削除しますか？")) return;
  updateContents<ArticleContent.Page>((draft) => {
    draft.sections = [...draft.sections.filter((_, i) => i !== index)];
  });
};
```

**問題点**:

- 削除確認のUI/APIロジックが複数の場所に散在
- `window.confirm` と Modal の混在
- エラーハンドリングの一貫性がない

**改善提案**:

```tsx
// ✅ hooks/useDeleteConfirm.ts
export const useDeleteConfirm = <T>(
  endpoint: string,
  onDeleted?: () => void
) => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const delete = async (item: T) => {
    if (!confirm(`本当に削除しますか？`)) return;

    try {
      setIsLoading(true);
      await axios.delete(endpoint, { data: item });
      onDeleted?.();
    } catch (err) {
      setError(extractErrorMessage(err));
    } finally {
      setIsLoading(false);
    }
  };

  return { delete, isLoading, error };
};

// ✅ components/DeleteConfirmModal.tsx
export const DeleteConfirmModal = ({
  item,
  title,
  message,
  onConfirm,
  onClose,
}: Props) => (
  <Modal title={title} onClose={onClose}>
    <p className="v2-text-body mb-4">{message}</p>
    <div className="flex gap-2 justify-end">
      <Button onClick={onClose} variant="subOutline">キャンセル</Button>
      <Button onClick={onConfirm} variant="danger">削除</Button>
    </div>
  </Modal>
);
```

**優先度**: 🟡 中  
**効果**: 削除ロジックの一貫性向上、エラーハンドリング統一

---

### 📌 パターン3：フォーム入力フィールド + エラー表示

**現状**:

```tsx
// TagModal.tsx
<div>
  <FormCaption>名前</FormCaption>
  <TextError>{getError("name")?.join("\n")}</TextError>
  <Input
    type="text"
    value={name}
    onChange={(e) => setName(e.target.value)}
    className="block w-full"
    maxLength={20}
  />
</div>

// MyListTable.tsx (同じパターン)
<div>
  <label htmlFor="title" className="v2-form-caption">
    <TextBadge variant="danger">必須</TextBadge>
    タイトル
  </label>
  <Input
    id="title"
    className="w-full"
    value={title}
    onChange={(e) => setTitle(e.target.value)}
    maxLength={120}
    required
  />
</div>

// ProfileForm.tsx (同じパターン)
<div>
  <FormCaption>説明</FormCaption>
  <TextError>{getError("user.profile.data.description")}</TextError>
  <Textarea
    className="w-full"
    value={user.profile.data.description || ""}
    rows={4}
    maxLength={1024}
    onChange={(e) => { ... }}
  />
</div>
```

**改善提案**:

共通フォームフィールドコンポーネント

```tsx
// ✅ components/form/FormField.tsx
type FormFieldProps = {
  label: string;
  error?: string;
  required?: boolean;
  children: React.ReactNode;
};

export const FormField = ({
  label,
  error,
  required,
  children,
}: FormFieldProps) => (
  <div>
    <FormCaption>
      {required && <TextBadge variant="danger">必須</TextBadge>}
      {label}
    </FormCaption>
    {error && <TextError>{error}</TextError>}
    {children}
  </div>
);

// 使用例
<FormField label="名前" error={getError("name")} required>
  <Input
    value={name}
    onChange={(e) => setName(e.target.value)}
    maxLength={20}
  />
</FormField>;
```

**優先度**: 🟡 中  
**効果**: フォーム実装の加速化、UIの一貫性向上

---

### 📌 パターン4：API エラーハンドリング + トースト通知

**現状**:

```tsx
// AddToMyList.tsx
try {
  const { data } = await axios.get("/api/v1/mylist");
  // ...
} catch (err) {
  setError(err instanceof Error ? err.message : "エラーが発生しました");
}

// MyListTable.tsx
try {
  await axios({ method, url, data });
  showSuccess("マイリストを更新しました");
  onSuccess();
} catch (err) {
  setError(extractErrorMessage(err));
}

// TagModal.tsx
try {
  const res = await axios.post(...);
  if ((res.status === 200 || res.status === 201) && onSave) {
    onSave(res.data.data);
  }
} catch (error) {
  if (isValidationError(error)) {
    setError(error);
  } else {
    handleErrorWithContext(error, { action: "save" });
  }
}
```

**問題点**:

- エラーハンドリング方式が3パターン混在
- `extractErrorMessage` vs `isValidationError` の使い分けが曖昧
- 成功メッセージの有無が統一されていない
- バリデーションエラーと一般エラーの処理が一貫していない

**改善提案**:

共通の API ラッパーHook

```tsx
// ✅ hooks/useApiCall.ts
export const useApiCall = () => {
  const { showSuccess, showError } = useToast();
  const [isLoading, setIsLoading] = useState(false);

  const call = async <T,>(
    apiCall: () => Promise<T>,
    onSuccess?: (data: T) => void,
    successMessage?: string
  ) => {
    try {
      setIsLoading(true);
      const data = await apiCall();
      if (successMessage) {
        showSuccess(successMessage);
      }
      onSuccess?.(data);
      return data;
    } catch (err) {
      if (isValidationError(err)) {
        return { validationErrors: err.response?.data.errors };
      } else {
        showError(extractErrorMessage(err));
      }
    } finally {
      setIsLoading(false);
    }
  };

  return { call, isLoading };
};

// 使用例
const { call } = useApiCall();
await call(
  () => axios.patch(`/api/v1/mylist/${list.id}`, data),
  (result) => onSuccess(),
  "マイリストを更新しました"
);
```

**優先度**: 🔴 高  
**効果**: コード削減20-30行/コンポーネント、エラーハンドリング統一

---

### 📌 パターン5：ページ初期化（Blade 埋め込みデータ読み込み）

**現状**:

```tsx
// MyListIndexPage.tsx
const app = document.getElementById("app-mylist-index");
if (app) {
  const App = () => {
    const [lists, setLists] = useState<MyListShow[]>(...);
    const [, setUser] = useState<User.MypageShow>(...);

    useEffect(() => {
      // API呼び出し
    }, []);
  };
}

// ProfileEditPage.tsx
const app = document.getElementById("app-profile-edit");
if (app) {
  const App = () => {
    const [user, setUser] = useState<User.MypageEdit>(
      JSON.parse(document.getElementById("data-user")?.textContent || "{}")
    );
    const [attachments, setAttachments] = useState<Attachment.MypageEdit[]>(...);
  };
}

// ArticleListPage.tsx
const app = document.getElementById("app-article-list");
if (app) {
  const user = JSON.parse(...);
  const articles = JSON.parse(...);
  // ...
}
```

**問題点**:

- 各ページで同じ Blade 読み込みパターンを繰り返している
- エラーハンドリングがページごとに異なる
- JSON.parse のエラー処理がない場所が多い

**改善提案**:

ページ初期化ユーティリティ

```tsx
// ✅ utils/bootpage.ts
export const getBootData = <T,>(elementId: string, defaultValue: T): T => {
  const element = document.getElementById(`data-${elementId}`);
  if (!element?.textContent) return defaultValue;

  try {
    return JSON.parse(element.textContent);
  } catch (err) {
    console.error(`Failed to parse ${elementId}:`, err);
    return defaultValue;
  }
};

export const mountApp = (appId: string, App: React.FC) => {
  const app = document.getElementById(appId);
  if (app) {
    createRoot(app).render(
      <ErrorBoundary name={appId}>
        <App />
      </ErrorBoundary>
    );
  }
};

// 使用例
const user = getBootData("user", {} as User.MypageShow);
const articles = getBootData("articles", [] as Article.MypageShow[]);

const App = () => {
  /* ... */
};
mountApp("app-article-list", App);
```

**優先度**: 🟡 中  
**効果**: ページ初期化の一貫性向上、10-15行削減/ページ

---

## 2. バックエンド（PHP / Laravel）の重複パターン

### 📌 パターン1：Repository の create/update/delete

**現状**:

```php
// MyListRepository.php
public function create(array $data): MyList
{
    return $this->model->create($data);
}

public function update(MyList $list, array $data): void
{
    $list->update($data);
}

public function delete(MyList $list): void
{
    $list->delete();
}

// TagRepository.php
// ほぼ同じ実装...

// UserRepository.php
// ほぼ同じ実装...
```

**現状の BaseRepository**:

```php
// BaseRepository.php
/**
 * @deprecated このクラスは非推奨です。継承せず、各Repositoryで必要なメソッドを個別に実装してください。
 */
abstract class BaseRepository
{
    final public function store(array $data) { /* ... */ }
    final public function update($model, array $data): void { /* ... */ }
    final public function delete(Model $model): void { /* ... */ }
}
```

**問題点**:

- BaseRepository が deprecated で、各Repository が個別実装している
- Trait で共通化するほうが適切
- ページネーション/フィルタのパターンも重複

**改善提案**:

Trait で共通化

```php
// ✅ Repositories/Concerns/HasCrud.php
trait HasCrud
{
    /**
     * 作成
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * 更新
     */
    public function update(Model $model, array $data): void
    {
        $model->update($data);
    }

    /**
     * 削除
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }
}

// MyListRepository.php
class MyListRepository
{
    use HasCrud;

    // リポジトリ固有のメソッドのみ実装
    public function paginateForUser(User $user, int $page, ...): Paginator { /* ... */ }
}
```

**優先度**: 🟡 中  
**効果**: 重複コード削減、保守性向上

---

### 📌 パターン2：Controller の CRUD テンプレート

**現状**:

| Controller        | メソッド数                        | パターン            |
| ----------------- | --------------------------------- | ------------------- |
| TagController     | 4 (index, store, update, destroy) | 標準CRUD            |
| MyListController  | 10+ (リスト + アイテム)           | 標準CRUD + 関連操作 |
| ArticleController | 6+ (ListPage, EditPage等)         | ページ返却          |

**共通化機会**:

```php
// ❌ 各Controller で繰り返し
public function index(Request $request)
{
    $user = $this->loggedinUser();
    $page = (int) $request->query('page', 1);
    $perPage = (int) $request->query('per_page', 20);
    $sort = (string) $request->query('sort', 'updated_at:desc');

    $items = $this->service->getItemsForUser($user, $page, $perPage, $sort);
    return ItemResource::collection($items);
}

// ✅ 提案：Trait で共通化
trait HasIndexAction
{
    abstract protected function getService();
    abstract protected function getResourceClass();

    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 20);
        $sort = (string) $request->query('sort', 'updated_at:desc');

        $items = $this->getService()->getItems($page, $perPage, $sort);
        return $this->getResourceClass()::collection($items);
    }
}
```

**優先度**: 🟡 中  
**効果**: Controller コード削減、一貫性向上

---

### 📌 パターン3：API レスポンス形式

**現状**:

```php
// MyListController.php
public function store(StoreMyListRequest $request): JsonResponse
{
    // ...
    return (new MyListShowResource($list))->response()->setStatusCode(201);
}

public function destroy(MyList $mylist): JsonResponse
{
    // ...
    return response()->json(status: 200);
}

// TagController.php
public function update(UpdateTagRequest $request, Tag $tag): TagResource
{
    // ...
    return new TagResource($tag);
}
```

**問題点**:

- `JsonResponse` vs `Resource` の使い分けが統一されていない
- ステータスコード設定の方法がばらばら
- エラーレスポンスの形式が統一されていない

**改善提案**:

レスポンス統一ヘルパー

```php
// ✅ Http/Responses/ApiResponse.php
class ApiResponse
{
    public static function created(Model $model, string $resourceClass = null): JsonResponse
    {
        if ($resourceClass) {
            return (new $resourceClass($model))->response()->setStatusCode(201);
        }
        return response()->json($model, 201);
    }

    public static function updated(Model $model): Model|JsonResponse
    {
        return $model;
    }

    public static function deleted(): JsonResponse
    {
        return response()->json(status: 204);
    }
}

// 使用例
return ApiResponse::created($list, MyListShowResource::class);
return ApiResponse::deleted();
```

**優先度**: 🟡 中  
**効果**: レスポンス形式の一貫性向上

---

### 📌 パターン4：バリデーション ルール

**現状**:

```php
// StoreMyListRequest.php
public function rules(): array
{
    return [
        'title' => 'required|string|min:1|max:120',
        'note' => 'nullable|string|max:65535',
        'is_public' => 'nullable|boolean',
    ];
}

// UpdateTagRequest.php
public function rules(): array
{
    return [
        'description' => 'nullable|string|max:1024',
    ];
}

// StoreTagRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|min:1|max:20|unique:tags,name',
        'description' => 'nullable|string|max:1024',
    ];
}
```

**改善提案**:

バリデーション ルール定数

```php
// ✅ App/Rules/ValidationRules.php
class ValidationRules
{
    const TITLE_RULES = 'required|string|min:1|max:120';
    const DESCRIPTION_RULES = 'nullable|string|max:1024';
    const SLUG_RULES = 'nullable|string|max:160|unique:mylists,slug';

    // ...
}

// StoreMyListRequest.php
public function rules(): array
{
    return [
        'title' => ValidationRules::TITLE_RULES,
        'note' => 'nullable|string|max:65535',
        'is_public' => 'nullable|boolean',
    ];
}
```

**優先度**: 🟢 低  
**効果**: バリデーションルール管理の一元化

---

## 3. 結合テスト（Laravel Feature）の重複パターン

### 📌 テストの共通化機会

**現状**:

```php
// tests/Feature/Controllers/Mypage/MyListController/IndexTest.php
public function test_authenticated_user_can_get_their_lists()
{
    $user = User::factory()->create();
    $lists = MyList::factory()->count(3)->for($user)->create();

    $response = $this->actingAs($user)
        ->getJson("/api/v1/mylist")
        ->assertStatus(200)
        ->assertJsonStructure(['data' => [...]]);
}

// tests/Feature/Controllers/Mypage/TagController/...Test.php
public function test_authenticated_user_can_get_tags()
{
    $user = User::factory()->create();
    $tags = Tag::factory()->count(3)->for($user)->create();

    $response = $this->actingAs($user)
        ->getJson("/api/v2/tags")
        ->assertStatus(200);
}
```

**改善提案**:

テスト基底クラス

```php
// ✅ tests/Feature/ControllerTestCase.php
abstract class ControllerTestCase extends TestCase
{
    protected function assertPaginatedIndex(
        string $endpoint,
        array $assertions = []
    ): TestResponse {
        return $this->actingAs($this->user)
            ->getJson($endpoint)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonPath('meta.per_page', 20);
    }

    protected function assertUnauthorized(
        string $method,
        string $endpoint
    ): TestResponse {
        return $this->json($method, $endpoint)
            ->assertStatus(401);
    }
}

// 使用例
public function test_authenticated_user_can_get_their_lists()
{
    $this->assertPaginatedIndex('/api/v1/mylist');
}
```

**優先度**: 🟡 中  
**効果**: テストコード削減、一貫性向上

---

## 4. 全体のコード重複の集計

### フロントエンド側

| パターン               | 重複箇所 | 削減可能行数 | 優先度 |
| ---------------------- | -------- | ------------ | ------ |
| CRUD モーダル          | 5+       | 80-100       | 🔴 高  |
| 削除ロジック           | 3+       | 40-50        | 🟡 中  |
| フォーム入力フィールド | 10+      | 100-150      | 🟡 中  |
| APIエラーハンドリング  | 8+       | 120-160      | 🔴 高  |
| ページ初期化           | 5+       | 50-75        | 🟡 中  |
| **合計**               | **31+**  | **390-535**  | -      |

### バックエンド側

| パターン        | 重複箇所 | 削減可能行数 | 優先度 |
| --------------- | -------- | ------------ | ------ |
| CRUD Repository | 8+       | 50-70        | 🟡 中  |
| CRUD Controller | 4+       | 40-60        | 🟡 中  |
| レスポンス形式  | 6+       | 20-30        | 🟡 中  |
| バリデーション  | 15+      | 30-50        | 🟢 低  |
| **合計**        | **33+**  | **140-210**  | -      |

---

## 5. リファクタ実施優先順位

### 🔴 Priority 1（即座に対応）

```
1. フロント: useApiCall Hook化
   - 影響度: 8+ コンポーネント
   - 削減行数: 120-160行
   - 所要時間: 3-4時間
   - リスク: 低（既存のAPIロジックを抽出するだけ）

2. バック: HasCrud Trait 導入
   - 影響度: 8+ Repository
   - 削減行数: 50-70行
   - 所要時間: 2時間
   - リスク: 低（既存の実装を Trait に移動するだけ）
```

### 🟡 Priority 2（次のリリース）

```
3. フロント: useModelModal Hook化
   - 削減行数: 80-100行
   - 影響度: 5+ モーダル
   - 所要時間: 3時間
   - リスク: 中（複数のモーダルにまたがる）

4. フロント: FormField コンポーネント導入
   - 削減行数: 100-150行
   - 影響度: 10+ フォーム
   - 所要時間: 4-5時間
   - リスク: 中

5. バック: Controller Trait （HasIndexAction等）
   - 削減行数: 40-60行
   - 影響度: 4+ Controller
   - 所要時間: 2-3時間
   - リスク: 低
```

### 🟢 Priority 3（段階的）

```
6. 削除ロジック共通化
7. バリデーションルール定数化
8. テスト基底クラス導入
```

---

## 6. 実装ロードマップ（Phase別）

### Phase 1（今週）：高優先度の Hook化

```
Week 1-2:
├─ useApiCall.ts 実装
├─ 既存コンポーネントで採用（MyListTable, AddToMyList等）
├─ テスト追加
└─ npm run check / npm run test 全PASS

所要時間: 3-4時間
影響範囲: 8+ フロントコンポーネント
```

### Phase 2（2-3週間後）：バックエンド Trait化

```
Week 3:
├─ HasCrud Trait 作成
├─ 各Repository に適用（MyList, Tag, User等）
├─ PHPStan/Pint チェック
└─ Feature テスト確認

所要時間: 2時間
影響範囲: 8+ Repository
```

### Phase 3（1か月後）：モーダル・フォーム統一

```
Week 4-5:
├─ useModelModal Hook化
├─ FormField コンポーネント
├─ 既存フォームで採用
└─ テスト・E2E 確認

所要時間: 6-8時間
影響範囲: 10+ フォーム・モーダル
```

---

## 7. チェックリスト

### 実装前

- [ ] 既存テスト全てPASS
- [ ] `npm run check` でエラーなし
- [ ] `php artisan test` でエラーなし
- [ ] git ブランチ作成

### useApiCall 実装時

- [ ] Hook の型定義を完全にする
- [ ] バリデーションエラー・一般エラーの処理を分岐
- [ ] トースト通知との連携を確認
- [ ] 5+ コンポーネントで試験採用
- [ ] 既存テスト・新規テスト両方で検証

### Repository Trait 適用時

- [ ] Trait メソッドの型安全性を確認
- [ ] 既存 Repository のテストが全て通るか確認
- [ ] BaseRepository の deprecated コメント更新
- [ ] PHPStan でエラーなし

### テスト実施

- [ ] 既存の Feature/Unit/Vitest テスト全てPASS
- [ ] 新しい共通化ロジックのテスト追加
- [ ] カバレッジ低下がないか確認

---

## 8. 参考：段階的実装による今後の対策

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

## 結論

**現状**:

- フロント側: 390-535行の削減可能な重複コード
- バック側: 140-210行の削減可能な重複コード
- 段階的実装による責務重複・共通化漏れが顕著

**推奨アクション**:

1. Phase 1：`useApiCall` Hook で即座に高ROI改善
2. Phase 2：Repository Trait で後続機能への下地作り
3. Phase 3：FormField など UI コンポーネント統一

**期待効果**:

- コード削減：500-750行
- テスト保守性向上
- 新機能開発速度向上（テンプレート化で20-30%高速化）
- バグ削減（共通化によるエラーハンドリング統一）
