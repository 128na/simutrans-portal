# PAK関連ドキュメント4件が架空の設計を記述していた件

ドキュメンテーション基盤移行（[docs/adr/0001](../adr/0001-services-actions-separation.md) 以降の一連の作業）の
過程で、`docs/knowledge/pak-parser-implementation-20260103-knowledge.md` を ADR 化するため
実装と突き合わせたところ、**記述内容が実装と一致しないどころか、対応する実装が一度も
存在しなかった**ことが判明した。

## 発見の経緯

ドキュメント最小化方針（実装とテストを SSOT とする）を適用する前段として、
「削除前に対応する実装が実在するか確認する」という手順（ドキュメンテーション基盤
テンプレート側の ADR-0002 で定めた確認手順）に従い、以下の4ファイルを実装と突き合わせた。

## 突き合わせ結果

| ドキュメント | 記述内容 | 実際の実装 |
| --- | --- | --- |
| pak-parser-implementation-20260103-knowledge.md | `FileInfoService::extract()`、`Extractors/File/Pak/{PakExtractor,PakFile,ObjectTypes}.php`、マジックナンバー `"PAKX"`、例外 `InvalidPakException` | 実際は `PakParser.php` / `PakHeader.php` / `BinaryReader.php` / `TypeParsers/*Parser.php`（14種類）。ディレクトリ構造・クラス名・マジックナンバー・例外名のいずれも一致しない |
| pak-parser-usage-20260103-knowledge.md | `FileInfoService->extract($file): array`、`Attachment->fileInfo`、`$metadata->pak_version` 等 | 実際の `FileInfoService` の公開メソッドは `updateOrCreateFromPak(Attachment): FileInfo` / `updateOrCreateFromZip(...)`。`FileInfo` モデル自体は実在するが、メソッドシグネチャが全く異なる |
| pak-parser-field-standards-spec.md | `VehicleType` / `CargoType` / `BuildingType` / `ClimaticZone` の4つの独立 Enum | 実際は `PakObjectType`（string backed enum）1本のみ、30種のケース（vehicle/building/bridge/tunnel/...）で表現。「気候帯」概念自体が実装に存在しない |
| pak-tunnel-format-spec.md | トンネルエントリのバイナリ構造（Type ID/Data Size/Tunnel Version/Way Type/Image Index/Portal Type/Tunnel Name）、バージョン履歴2015〜2025年 | 実際の `TunnelParser.php` はバージョン1〜6のフィールド差分（topspeed/price/maintenance/waytype/intro_date/retire_date/axle_load/number_of_seasons/has_way/broad_portals）で、記述内容と一致するフィールドがほぼない |

4ファイルとも「最終更新: 2025-11-24」または類似の日付が付されていたが、その時点の実装や
それ以前の実装のいずれとも対応しない。ドリフト（後から実装が変わった）ではなく、
**最初から実装を反映していなかった**と判断できる。

## 対応

4ファイルは削除した（[docs/adr/0001](../adr/0001-services-actions-separation.md) 以降の
ドキュメンテーション基盤移行の一環）。他ファイルからの参照は自己参照のみで、外部からの
参照は無かったため実質的な影響はない。実際の PAK パーサーは `PakParserTest.php`
（687行・19テストケース）で十分に検証されており、実装とテストが SSOT として機能する。

## 教訓

- ドキュメントは実装と同時に検証されない限り、内容が事実かどうかを担保する仕組みがない。
  今回の4ファイルは長期間（2025-11-24 〜 少なくとも本記録作成時点の2026-08-25まで）
  誰にも実装と突き合わされずに残存していた。
- 「テストが存在するか」だけでなく「ドキュメントが指す実装が実在するか」を検証する
  ステップ（template ADR-0002 の確認手順）を経なければ、この種の架空記述は
  レビューをすり抜ける。
- 影響範囲: これらのドキュメントを参照して開発した形跡は見当たらない（実装コードが
  ドキュメントの記述と異なる独自の設計で一貫していたため）。ただし今後 AI エージェントが
  これらのドキュメントを文脈として読み込んだ場合、実在しないクラス名を実装しようとする
  リスクがあった。
