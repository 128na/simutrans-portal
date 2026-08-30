# 本番の CACHE_DRIVER=apc が CLI プロセス間で永続化されず、増分バックアップ集計が機能していなかった件

さくらインターネット不正アクセス事案を受けた認証情報ローテーション作業（この作業自体の記録はリポジトリ外の個人ローカル環境にのみ存在するため、本リポジトリからは参照不可）の過程で、Dropboxバックアップの動作確認をしていたところ、`backup:sync-uploads`（ユーザーアップロードファイルのrclone差分同期、`app/Actions/Backup/SyncUserUploads.php`）が2026-08-28のデプロイ以降、cron経由では一切進捗していないことが判明した。

## 症状

- ローカルへの差分同期（`backups/user-uploads/`）は2773/2773ファイルで完全に一致 = 正常
- Dropbox側の同期先（`user-uploads/`）は同期先ディレクトリ自体が存在せず、1件も転送されていなかった
- `SyncUploadsDigestTracker` の日次集計（`backup:report-sync-uploads-digest` → Discord通知）は常に `transferred=0, errors=0`
- `artisan backup:sync-uploads` を対話的なSSHセッションから直接（フォアグラウンドで）実行すると、rclone認証・転送とも正常に成功する（実際に45ファイル転送を確認）
- `artisan schedule:run` 経由（`runInBackground()`によるバックグラウンド起動）で実行すると、プロセスの痕跡もログ出力も一切残らない

## 原因

`.env` の `CACHE_DRIVER=apc` に対し、本番サーバーの `php.ini` は `apc.enable_cli => Off` になっていた（デプロイ後にキャッシュが更新されない別の不具合への対策として、以前から意図的にCLI向けAPCuを無効化していた）。

APCu拡張自体はロードされているため `Cache::put()`/`Cache::increment()` はエラーなく成功するが、`apc.enable_cli=Off` の状態ではCLIプロセス間でメモリが共有されない。実験で直接確認:

```
process 1: Cache::put("test", "hello", 60);  → 成功
process 2 (別プロセス): Cache::get("test");   → NULL
```

これにより、CLIプロセスをまたいだ状態共有に依存する2つの仕組みが静かに壊れていた:
- `SyncUploadsDigestTracker`（`Cache::increment()`で集計 → 別プロセスの`backup:report-sync-uploads-digest`が読む）
- `Schedule::withoutOverlapping()`（重複防止ロックもキャッシュ経由）

`runInBackground()`自体は無関係だった。原因を`CACHE_DRIVER=database`に切り替えて解消した後、同じ`schedule:run`→バックグラウンド実行の経路で51ファイルの実転送・正しい集計記録を確認済み。

## 対応

本番 `.env` の `CACHE_DRIVER` を `apc` → `database` に変更（`cache`/`cache_locks` テーブルは既存だったためマイグレーション不要）。`apc.enable_cli`は意図的な設定のため変更していない。

## 教訓

- 共有レンタルサーバーでCLI(cron)から使う機能に`Cache`ファサードを使う場合、`CACHE_DRIVER`がCLIプロセス間で実際に永続化されるストアか確認すること。`apc.enable_cli`のようなphp.ini側の設定は`.env`の`CACHE_DRIVER`と独立しており、片方だけ変更すると整合性が取れなくなる。
- 「例外が発生しない」ことは「正しく動作している」ことの証明にならない。`Cache::put()`はAPCu CLI無効時もエラーを出さずに"成功"するため、この種の静かな機能不全は例外監視では検知できない。実際に別プロセスから読み出して確認する必要がある。
- スケジュールされたコマンドの動作確認は、手動でフォアグラウンド実行した結果だけでなく、`artisan schedule:run`を経由した実際の実行経路（バックグラウンド起動含む）で行うべき。今回、直接実行は最初から成功していたため、原因調査が長引いた。
