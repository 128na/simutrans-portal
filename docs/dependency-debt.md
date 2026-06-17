# Dependency Debt

上げられないメジャー依存の台帳。dependabot が再提案しても、ここに記録済みのものは「既知の負債」として扱う。
解除条件を満たしたら対応し、この表から削除する。「未マージPR数」ではなくこの表を棚卸し・監査の対象にする。

| Package | Current | Target | Blocker | Type | Revisit condition | Recorded |
|---------|---------|--------|---------|------|-------------------|----------|
| laravel/framework | 12.62 | 13.x | prod が PHP 8.3。Laravel 13.3+ は Symfony 8 を引き込み PHP 8.4.1+ が必須 | infra | prod を PHP 8.4+ に更新する | 2026-06-17 |

<!--
運用メモ:
- composer.json は laravel/framework を ^12 に固定しているため通常は 13 のPRは出ないが、
  意図を明示するため .github/dependabot.yml の composer に ignore(semver-major) も追加している。
- prod PHP を 8.4+ にできたら、本行を削除し Laravel 13 移行を実施する。
-->
