# Dependency Debt

上げられないメジャー依存の台帳。dependabot が再提案しても、ここに記録済みのものは「既知の負債」として扱う。
解除条件を満たしたら対応し、この表から削除する。「未マージPR数」ではなくこの表を棚卸し・監査の対象にする。

| Package | Current | Target | Blocker | Type | Revisit condition | Recorded |
|---------|---------|--------|---------|------|-------------------|----------|
| laravel/framework | 12.63 | 13.x | prod が PHP 8.3。Laravel 13.3+ は Symfony 8 を引き込み PHP 8.4.1+ が必須 | infra | prod を PHP 8.4+ に更新する | 2026-06-17 |
| markdown-it (npm) | 14.3.0 | 15.x | 内部で linkify-it が v6 化され、fuzzy link(裸ドメイン等の自動リンク化)が既定offになる。`resources/js/features/articles/components/postType/Markdown.tsx` は `linkify: true` でユーザー投稿記事本文を描画しており、専用のレンダリングテストも無いため、既存記事の表示が無言で変わるリスクがある。加えて `@types/markdown-it` はv15で同梱型と衝突するため削除が必要 | behavior-change | Markdown描画の自動テストを追加し、fuzzy link無効化後の表示を確認できるようにしてから移行する | 2026-08-03 |

<!--
運用メモ:
- composer.json は laravel/framework を ^12 に固定しているため通常は 13 のPRは出ないが、
  意図を明示するため .github/dependabot.yml の composer に ignore(semver-major) も追加している。
- prod PHP を 8.4+ にできたら、本行を削除し Laravel 13 移行を実施する。
-->
