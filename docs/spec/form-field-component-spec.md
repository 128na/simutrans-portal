# FormField コンポーネント設計仕様

キーワード: コンポーネント設計, フォーム, 再利用性, TypeScript
最終更新日：2026-01-11
ステータス：設計フェーズ

## 概要

フォーム要素の実装を加速化するため、フィールド + ラベル + エラー表示をまとめた再利用可能な `FormField` コンポーネントの設計仕様。

現在、TagModal や MyListEditModal などで以下パターンが繰り返されている：

```tsx
<div>
  <FormCaption>{label}</FormCaption>
  <TextError>{error}</TextError>
  <Input value={value} onChange={onChange} />
</div>
```

このパターンを統一コンポーネント化することで、100-150行の削減と実装速度向上を見込む。

---

## 1. 設計方針

### 目標

1. **コード削減**：10+ フォーム場所で 100-150行削減
2. **一貫性**：全フォーム要素の見た目・動作を統一
3. **拡張性**：今後のフォーム追加も同一パターンで対応可能

### アーキテクチャ

```tsx
// ✅ 推奨構成
<FormField label="タイトル" error={error} required>
  <Input value={title} onChange={handleChange} />
</FormField>
```

---

## 2. FormField コンポーネント仕様

### Props

```typescript
type FormFieldProps = {
  /** フィールドラベル */
  label: string;

  /** エラーメッセージ（文字列または文字列配列） */
  error?: string | string[] | null;

  /** 必須フィールドか（アスタリスク表示用） */
  required?: boolean;

  /** フィールドの説明テキスト */
  hint?: string;

  /** フォーム要素（Input, Textarea等） */
  children: React.ReactNode;

  /** カスタムCSS クラス */
  className?: string;
};
```

### JSX構造

```tsx
<div className={className}>
  <FormCaption>
    {required && <TextBadge variant="danger">必須</TextBadge>}
    {label}
  </FormCaption>

  {error && (
    <TextError>{Array.isArray(error) ? error.join("\n") : error}</TextError>
  )}

  {children}

  {hint && <TextSub>{hint}</TextSub>}
</div>
```

---

## 3. 使用例

### 基本的な使用法

```tsx
// ✅ 単純な入力フィールド
<FormField label="タイトル" error={getError("title")} required>
  <Input
    value={title}
    onChange={(e) => setTitle(e.target.value)}
    maxLength={120}
  />
</FormField>
```

### テキストエリア

```tsx
// ✅ 複数行入力
<FormField label="説明" error={getError("description")}>
  <Textarea
    value={description}
    onChange={(e) => setDescription(e.target.value)}
    rows={4}
    maxLength={1024}
  />
</FormField>
```

### チェックボックス

```tsx
// ✅ チェックボックス
<FormField label="公開設定">
  <Checkbox checked={isPublic} onChange={(e) => setIsPublic(e.target.checked)}>
    このリストを公開する
  </Checkbox>
</FormField>
```

### セレクトボックス

```tsx
// ✅ ドロップダウン
<FormField label="カテゴリ" error={getError("category")} required>
  <Select value={category} onChange={(e) => setCategory(e.target.value)}>
    <option value="">選択してください</option>
    {categories.map((cat) => (
      <option key={cat.id} value={cat.id}>
        {cat.name}
      </option>
    ))}
  </Select>
</FormField>
```

### ヒント付き

```tsx
// ✅ 説明付きフィールド
<FormField
  label="メモ"
  hint="このリストについてのメモを入力できます（オプション）"
>
  <Textarea value={note} onChange={(e) => setNote(e.target.value)} />
</FormField>
```

---

## 4. 既存実装との変換例

### Before（現状）

```tsx
// TagModal.tsx での実装（重複パターン）
<div>
  <FormCaption>
    <TextBadge variant="danger">必須</TextBadge>
    名前
  </FormCaption>
  <TextError>
    {(() => {
      const nameError = getError("name");
      if (Array.isArray(nameError)) {
        return nameError.join("\n");
      }
      return nameError || undefined;
    })()}
  </TextError>
  <Input
    type="text"
    value={name}
    onChange={(e) => setName(e.target.value)}
    className="block w-full"
    maxLength={20}
    required
  />
</div>

<div>
  <FormCaption>説明</FormCaption>
  <TextError>
    {(() => {
      const descError = getError("description");
      if (Array.isArray(descError)) {
        return descError.join("\n");
      }
      return descError || undefined;
    })()}
  </TextError>
  <Textarea
    rows={4}
    value={description}
    onChange={(e) => setDescription(e.target.value)}
    className="block w-full"
    maxLength={1024}
  />
</div>
```

### After（FormField使用）

```tsx
// FormField で統一（30行削減）
<FormField label="名前" error={getError("name")} required>
  <Input
    value={name}
    onChange={(e) => setName(e.target.value)}
    maxLength={20}
  />
</FormField>

<FormField label="説明" error={getError("description")}>
  <Textarea
    rows={4}
    value={description}
    onChange={(e) => setDescription(e.target.value)}
    maxLength={1024}
  />
</FormField>
```

**削減**: 約30行

---

## 5. 実装計画

### ステップ 1：コンポーネント実装

**ファイル**: `resources/js/components/form/FormField.tsx`

**所要時間**: 1時間

```tsx
// ✅ 実装例
export const FormField: React.FC<FormFieldProps> = ({
  label,
  error,
  required,
  hint,
  children,
  className,
}) => {
  return (
    <div className={className}>
      <FormCaption>
        {required && <TextBadge variant="danger">必須</TextBadge>}
        {label}
      </FormCaption>
      {error && (
        <TextError>{Array.isArray(error) ? error.join("\n") : error}</TextError>
      )}
      {children}
      {hint && <TextSub>{hint}</TextSub>}
    </div>
  );
};
```

### ステップ 2：既存フォームの統合

**対象**: 10+ フォーム場所

| ファイル        | 削減予定 | 優先度 |
| --------------- | -------- | ------ |
| TagModal.tsx    | 20行     | 🔴 高  |
| MyListEditModal | 25行     | 🔴 高  |
| ProfileForm.tsx | 30行     | 🔴 高  |
| ArticleForm.tsx | 35行     | 🟡 中  |
| その他フォーム  | 40行+    | 🟡 中  |

**所要時間**: 3時間

### ステップ 3：テスト追加

**内容**:

- FormField 単体テスト（Props の各組み合わせ）
- 既存フォームのテスト確認（動作変化がないことを確認）

**所要時間**: 1時間

---

## 6. テスト計画

### FormField のテスト項目

```typescript
// ✅ テスト例
describe("FormField", () => {
  it("ラベルを表示する", () => {
    const { getByText } = render(
      <FormField label="テスト"><Input /></FormField>
    );
    expect(getByText("テスト")).toBeInTheDocument();
  });

  it("required時に必須バッジを表示", () => {
    const { getByText } = render(
      <FormField label="必須項目" required><Input /></FormField>
    );
    expect(getByText("必須")).toBeInTheDocument();
  });

  it("エラーメッセージを表示", () => {
    const { getByText } = render(
      <FormField label="テスト" error="エラーです"><Input /></FormField>
    );
    expect(getByText("エラーです")).toBeInTheDocument();
  });

  it("エラー配列は改行で結合", () => {
    const { getByText } = render(
      <FormField label="テスト" error={["エラー1", "エラー2"]}><Input /></FormField>
    );
    expect(getByText("エラー1\nエラー2")).toBeInTheDocument();
  });

  it("hint を表示", () => {
    const { getByText } = render(
      <FormField label="テスト" hint="ヒント"><Input /></FormField>
    );
    expect(getByText("ヒント")).toBeInTheDocument();
  });
});
```

---

## 7. パフォーマンス・メンテナンス影響

### メリット

1. **開発速度向上**：フォーム実装時間が 20-30% 削減
2. **一貫性向上**：全フォーム要素の見た目が統一される
3. **保守性向上**：ラベル・エラー表示の変更が一箇所で済む
4. **アクセシビリティ**：コンポーネント内で統一的な ARIA 属性設定が可能

### デメリット

1. **Props 増加**：複雑な要件に対応する場合、Props が増える可能性
2. **カスタマイズ制限**：統一フォーマットから外れたカスタマイズが難しい

**対策**: `className` Props でカスタムCSS を許容

---

## 8. 実装スケジュール

| フェーズ           | 所要時間  | 予定開始 | 状態      |
| ------------------ | --------- | -------- | --------- |
| コンポーネント実装 | 1時間     | Sprint 2 | ⬜ 未開始 |
| 既存フォーム統合   | 3時間     | Sprint 2 | ⬜ 未開始 |
| テスト作成・検証   | 1時間     | Sprint 2 | ⬜ 未開始 |
| **合計**           | **5時間** | -        | -         |

---

## 関連ドキュメント

- [コード重複パターン分析](../knowledge/code-duplication-patterns-20260111-knowledge.md)
- [リファクタリング実装ログ](../log/refactoring-priority1-2-20260111-log.md)
