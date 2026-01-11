# トースト通知システム 実装計画

最終更新: 2026-01-11
ステータス: 計画中
対象: フロントエンド全体（React島＋グローバル通知）

## 概要

現在のエラー/成功通知が `window.alert()` 中心で UX が悪く、マイリスト追加などの操作完了が視覚的に分かりづらい。トースト通知システムを導入し、ユーザーフィードバックを改善する。

## 現状の問題点

### 1. 成功通知が不足

- **マイリスト追加**: 成功時にモーダルが閉じるのみ（通知なし）
- **リスト作成/編集/削除**: 成功後リストを再取得するのみ
- **アイテム削除/メモ編集**: 同上
- **→ ユーザーが操作完了を認識できない**

### 2. エラー通知が侵襲的

- `window.alert()` 使用（モーダルダイアログでブロッキング）
- UX的に好ましくない
- `lib/errorHandler.ts` に TODO コメントあり

### 3. トースト機能が限定的

- `lib/copyText.ts` に簡易版が存在
- 成功通知のみ（緑色固定）
- v2-\* デザインシステムと統一されていない
- 複数表示の制御なし

### 4. 通知の使い分けが不明確

- バリデーションエラー → ローカルステート（適切）
- APIエラー → `window.alert()`（改善必要）
- 成功通知 → なし（改善必要）
- レンダリングエラー → ErrorBoundary（適切）

## 実装方針

### 優先度1: トースト通知システムの構築

#### 1-1. Toast コンポーネント作成

- **ファイル**: `resources/js/components/ui/Toast.tsx`
- **機能**:
  - 4種類のバリアント: `success` / `error` / `warning` / `info`
  - v2-\* デザインシステム準拠（色・フォント・シャドウ）
  - アイコン表示（種類ごと）
  - 閉じるボタン
  - 自動消去（デフォルト3秒、カスタマイズ可能）
  - アニメーション（フェードイン/アウト、スライド）
- **デザイン**:
  - 位置: 右下固定（`fixed bottom-4 right-4`）
  - 複数表示時はスタック（下から上に積み上げ）
  - 最大5件まで（古いものから自動削除）
  - モバイル対応（中央寄せ、幅調整）

#### 1-2. ToastContainer コンポーネント作成

- **ファイル**: `resources/js/components/ui/ToastContainer.tsx`
- **機能**:
  - 複数のトーストを管理
  - z-index 制御（モーダルより上）
  - スタック表示（縦並び）

#### 1-3. useToast フック作成

- **ファイル**: `resources/js/hooks/useToast.ts`
- **機能**:
  - `showSuccess(message, duration?)`: 成功通知
  - `showError(message, duration?)`: エラー通知
  - `showWarning(message, duration?)`: 警告通知
  - `showInfo(message, duration?)`: 情報通知
  - `dismiss(id)`: 特定トーストを閉じる
  - `dismissAll()`: 全トーストを閉じる
- **実装**:
  - Context API でグローバル状態管理
  - トースト ID 自動生成（uuid or timestamp）
  - 重複メッセージの制御（同じメッセージは5秒以内は無視）

#### 1-4. ToastProvider 作成

- **ファイル**: `resources/js/providers/ToastProvider.tsx`
- **配置**: 各エントリーポイント（`mypage.ts`, `front.ts` など）でルートをラップ

### 優先度2: 既存コードへの適用

#### 2-1. マイリスト機能

- **AddToMyList.tsx**:
  - アイテム追加成功時: `showSuccess("マイリストに追加しました")`
  - 新規リスト作成成功時: `showSuccess("マイリストを作成しました")`
- **MyListTable.tsx**:
  - リスト作成成功時: `showSuccess("マイリストを作成しました")`
  - リスト編集成功時: `showSuccess("マイリストを更新しました")`
  - リスト削除成功時: `showSuccess("マイリストを削除しました")`
  - 公開URLコピー: 既存のトーストを新システムに移行
- **MyListItemsTable.tsx**:
  - メモ保存成功時: `showSuccess("メモを保存しました")`
  - アイテム削除成功時: `showSuccess("アイテムを削除しました")`
  - 並び替え成功時: `showSuccess("並び替えを保存しました")`

#### 2-2. エラーハンドラー統合

- **lib/errorHandler.ts**:
  - `handleError()` の `window.alert()` を `showError()` に置き換え
  - バリデーションエラー（422）は引き続きローカルステートで表示
  - ネットワークエラーはトーストで通知
- **適用範囲**:
  - マイリスト以外の全 API 呼び出し
  - 既存の `window.alert()` 使用箇所

#### 2-3. copyText.ts の統合

- **lib/copyText.ts**:
  - `showToast()` を新システムに統合
  - `copyToClipboard()` は成功時に `showSuccess()` を呼び出すよう変更
  - 後方互換性のため既存関数は残す（deprecated マーク）

### 優先度3: テスト追加

#### 3-1. コンポーネントテスト（Vitest）

- `__tests__/components/ui/Toast.test.tsx`:
  - 各バリアントの表示確認
  - 自動消去のタイミング
  - 閉じるボタンの動作
- `__tests__/components/ui/ToastContainer.test.tsx`:
  - 複数トーストのスタック表示
  - 最大件数制御
- `__tests__/hooks/useToast.test.tsx`:
  - 各メソッドの動作確認
  - 重複制御

#### 3-2. 統合テスト

- マイリスト機能のトースト表示確認（既存テストに追加）

## タスク一覧

### Phase 1: 基盤構築（優先）

- [ ] 1. Toast コンポーネント作成
  - [ ] バリアント実装（success/error/warning/info）
  - [ ] アニメーション追加
  - [ ] 閉じるボタン
  - [ ] モバイル対応
- [ ] 2. ToastContainer コンポーネント作成
  - [ ] スタック表示
  - [ ] 最大件数制御
- [ ] 3. useToast フック作成
  - [ ] Context API セットアップ
  - [ ] show\* メソッド実装
  - [ ] 重複制御
- [ ] 4. ToastProvider 作成 & 配置
  - [ ] mypage.ts に適用
  - [ ] front.ts に適用

### Phase 2: マイリスト機能への適用

- [ ] 5. AddToMyList.tsx に成功通知追加
  - [ ] アイテム追加成功
  - [ ] 新規リスト作成成功
- [ ] 6. MyListTable.tsx に成功通知追加
  - [ ] リスト作成/編集/削除成功
  - [ ] 公開URLコピーを新システムに移行
- [ ] 7. MyListItemsTable.tsx に成功通知追加
  - [ ] メモ保存成功
  - [ ] アイテム削除成功
  - [ ] 並び替え成功

### Phase 3: エラーハンドラー統合

- [ ] 8. lib/errorHandler.ts を更新
  - [ ] `window.alert()` を `showError()` に置き換え
  - [ ] useToast フック統合
- [ ] 9. lib/copyText.ts を更新
  - [ ] `showToast()` を deprecated マーク
  - [ ] `copyToClipboard()` を新システムに対応

### Phase 4: テスト追加

- [ ] 10. Toast コンポーネントテスト作成
- [ ] 11. ToastContainer テスト作成
- [ ] 12. useToast フックテスト作成
- [ ] 13. 既存テストの更新（マイリスト機能）

### Phase 5: 品質チェック

- [ ] 14. TypeScript型チェック（`npm run check`）
- [ ] 15. 動作確認（手動テスト）
  - [ ] 各トーストバリアントの表示
  - [ ] 複数トーストの表示
  - [ ] モバイル表示
  - [ ] マイリスト操作の成功通知
- [ ] 16. ドキュメント更新
  - [ ] README.md にトースト使用方法を追記
  - [ ] 既存の TODO コメント削除

## 技術仕様

### Toast コンポーネント API

```typescript
interface ToastProps {
  id: string;
  message: string;
  variant: "success" | "error" | "warning" | "info";
  duration?: number; // ミリ秒、デフォルト3000
  onClose: (id: string) => void;
}
```

### useToast フック API

```typescript
interface ToastOptions {
  duration?: number; // ミリ秒、デフォルト3000
  dismissPrevious?: boolean; // 既存トーストを閉じるか
}

interface UseToastReturn {
  showSuccess: (message: string, options?: ToastOptions) => string;
  showError: (message: string, options?: ToastOptions) => string;
  showWarning: (message: string, options?: ToastOptions) => string;
  showInfo: (message: string, options?: ToastOptions) => string;
  dismiss: (id: string) => void;
  dismissAll: () => void;
}
```

### デザインシステム統合

```typescript
// v2-* クラスとの統合
const variantClasses = {
  success: "bg-green-50 border-green-300 text-green-800",
  error: "bg-red-50 border-red-300 text-red-800",
  warning: "bg-yellow-50 border-yellow-300 text-yellow-800",
  info: "bg-blue-50 border-blue-300 text-blue-800",
};

// アイコン
const variantIcons = {
  success: "✓",
  error: "✕",
  warning: "⚠",
  info: "ℹ",
};
```

## 適用後の通知フロー

### 成功時

1. API呼び出し成功
2. `showSuccess("操作完了メッセージ")` 呼び出し
3. トースト表示（緑色、3秒後自動消去）
4. 必要に応じてデータ再取得

### エラー時

1. API呼び出し失敗
2. `extractErrorMessage(error)` でメッセージ抽出
3. バリデーションエラー（422）の場合:
   - ローカルステートに `setError(message)` → フォーム下に表示
4. その他のエラー:
   - `showError(message)` 呼び出し → トースト表示（赤色、3秒後自動消去）

### バリデーションエラー（変更なし）

- フォーム内に `<div className="v2-card v2-card-danger">` で表示
- トーストは使用しない（フォーム近くに表示する方が UX 的に適切）

## 既存コードとの互換性

### ErrorBoundary（変更なし）

- Reactレンダリングエラーのキャッチ → フォールバックUI表示
- `silent: true` でエラーログのみ
- トースト通知は使用しない

### セッションメッセージ（Blade）（変更なし）

- `session('success')` / `session('error')` → Blade側で表示
- React島の外で発生するため、トーストは使用しない

## 参考リソース

- 既存実装: `resources/js/lib/copyText.ts`
- エラーハンドラー: `resources/js/lib/errorHandler.ts`
- ErrorBoundary: `resources/js/components/ErrorBoundary.tsx`
- デザインシステム: v2-\* クラス（Tailwind CSS）

## リスク / 代替案

### リスク

- トースト表示が多すぎるとUXが悪化 → 適切な箇所のみに適用
- 複数トーストの重複 → 重複制御機能で対応
- モーダルとトーストの z-index 競合 → モーダル（z-50）より上（z-[60]）に設定

### 代替案

- **外部ライブラリ使用**: react-toastify, sonner など
  - メリット: 実装時間短縮、機能豊富
  - デメリット: バンドルサイズ増加、v2-\* デザインとの統一が困難
  - **判断**: 自前実装を選択（軽量、デザイン統一、学習コスト低）

## 実装後の期待効果

1. **UX改善**: ユーザーが操作完了を視覚的に認識できる
2. **一貫性**: 全体で統一された通知システム
3. **保守性**: エラーハンドリングの一元管理
4. **拡張性**: 将来的な機能追加（アクションボタン、プログレスバーなど）が容易
