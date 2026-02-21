# Testing Skill

## 目的

変更の妥当性を短いフィードバックループで検証し、安全にリリース可能な状態を維持する。

## 基本方針

- 影響範囲が狭い修正は `npm run check` と `npm run test` を通す
- 変更箇所に近いテストから先に実行する
- 必要なら最小限のテストを追加する
- `npm run check` は整形・lint 自動修正を含む（作業ツリー差分に注意）

## 実行手順

1. まず関連テスト（ある場合）を実行する
   - フロント: `npm run test:front`
   - バックエンド Unit: `npm run test:back:unit`
   - バックエンド Feature: `npm run test:back:feature`
2. `npm run check` を実行する
3. `npm run test` を実行する
4. 失敗時は変更起因かを切り分ける

## チェックポイント

- 変更に対するテスト観点が不足していない
- 既知の既存失敗と今回起因の失敗を区別して報告する
- 実行結果を簡潔に共有する
