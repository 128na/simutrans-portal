# PAKパーサー メモリ最適化ログ

キーワード: メモリ最適化, ベンチマーク, pakパーサー, Node, BinaryReader
最終更新日：2026-04-19
ステータス：完了

## 概要

ZIPファイル内の大容量PAKファイルをパースする際にメモリが急増し、OOMが発生する問題を調査・改善した。
ベンチマーク（最悪3ファイル）を軸に問題を特定し、コード改修で効果を確認するサイクルで解決した。

---

## 改善フロー

### 1. OOM発生の検知

`php artisan attachment:reparse-pak-files --max-size=0 --sync` でメモリ上限（512MB）超えが発生。
897/899件が成功したが2件はOOM終了。

**原因1（即時解決）：ZipArchiveParser のバイナリ判定が `.pak` だけをバイナリ扱い**

```php
// Before: .pak のみバイナリ
private function isBinaryFile(string $filename): bool
{
    return str_ends_with(strtolower($filename), '.pak');
}

// After: テキスト拡張子ホワイトリスト方式
private function isBinaryFile(string $filename): bool
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return ! in_array($ext, ['dat', 'txt', 'tab', 'md'], true);
}
```

BMPなど大容量バイナリが `mb_convert_encoding` を通っていたため最大2倍のメモリを消費していた。
ホワイトリスト方式に変更し、テキストファイル以外はエンコード変換をスキップするように修正。

---

### 2. ベンチマーク対象の選定

**メモリログの追加**（`ReparsePakFilesCommand::reparseAttachment()`）:

```php
$peakBefore = memory_get_peak_usage(true);
// ... dispatch job ...
$peakAfter = memory_get_peak_usage(true);

if ($peakAfter > $peakBefore) {
    Log::debug('New peak memory during pak reparse', [
        'attachment_id' => $attachment->id,
        'filename' => $attachment->original_name,
        'file_size_mb' => round($attachment->size / 1048576, 2),
        'peak_before_mb' => round($peakBefore / 1048576, 2),
        'peak_after_mb' => round($peakAfter / 1048576, 2),
        'peak_delta_mb' => round(($peakAfter - $peakBefore) / 1048576, 2),
    ]);
}
```

> **注意**: `memory_get_peak_usage(true)` はプロセス全体の累積峰値（単調増加）。
> 全体一括処理のデルタ値は前ファイルで既に峰値が上昇している場合に過小評価される。
> 正確な単独ファイルの消費量を測るには `--sync` で1ファイルずつ実行する。

全体スキャン後のログで上位3件を特定：

| ID | ファイル名 | サイズ | ピーク増加 |
|----|-----------|--------|-----------|
| 2299 | German市内建築移植v2.zip | 97.5 MB | +344 MB |
| 3362 | SKS_SimplePostIndustry_ver1.zip | 36 MB | +16 MB ※ |
| 471 | Simutrans-Tree.zip | 30 MB | +50 MB ※ |

※ 全体scan中のデルタは前ファイルで既に峰値が上がっていたため過小評価。
単独実行では SKS +94 MB、Tree +78 MB が実際の消費量。

---

### 3. 根本原因の分析

**Node::parse() の2重メモリ確保問題**

改修前、Node構築時にデータをコピーしていた：

```
パース中のメモリ状態（改修前）:
  BinaryReader.binary: F バイト（ファイル全体）
  + 各Nodeのdata文字列の総和 ≈ F バイト（全ノードを同時保持）
  ─────────────────────────────────────────────
  ピーク ≈ 2F バイト
```

- `Node::parse()` で `$reader->readString($size)` を呼ぶたびに `$node->data` に部分文字列コピーが発生
- ツリー構築完了時点で BinaryReader が全バイナリ（F）＋全ノードのデータコピー（≈F）を同時保持
- 97.5 MB ZIPでは内部のPAKファイル1つが数十MBになり得る

---

### 4. 改修内容

**A. Node: データを遅延生成（オフセット記録方式）**

```php
// Before
public string $data,  // パース時に全コピー

// After
private readonly string $sourceBinary,  // 全バイナリへの参照（CoW）
private readonly int $dataOffset,       // データ開始位置

public function __get(string $name): mixed
{
    if ($name === 'data') {
        return $this->size > 0
            ? substr($this->sourceBinary, $this->dataOffset, $this->size)
            : '';
    }
    throw new \RuntimeException("Undefined property Node::\${$name}");
}
```

全ノードが同一バイナリ文字列をPHP CoW参照で共有するため、ツリー構築中の余分なコピーがなくなる。
`$node->data` アクセス時のみ一時的な部分文字列を生成（使用後即解放）。

**B. BinaryReader: getBinary() 追加**

```php
public function getBinary(): string
{
    return $this->binary;
}
```

Node が sourceBinary を保持するための参照取得メソッド。

**C. PakParser: 参照を早期解放**

```php
$reader = new BinaryReader($binary);
unset($binary);  // BinaryReader が保持するので不要
$root = Node::parse($reader);
unset($reader);  // Nodeツリーが sourceBinary を保持するので不要
```

**D. FileInfoService: ZIPエントリの早期解放**

```php
$isBinary = $fileData['is_binary'];
$content = $fileData['content'];
unset($fileData);  // 配列ラッパーを早期解放

$data = $this->handleExtractors($filename, $content, $data);
unset($content);   // 抽出完了後にバイナリを即解放
```

---

### 5. 効果測定

単独実行（`php artisan attachment:reparse-pak-files {id} --sync`）で比較：

| ファイル | サイズ | 改修前(全体scan) | 改修後(単独実行) | 改善 |
|---------|--------|-----------------|-----------------|------|
| German市内建築移植v2.zip | 97.5 MB | +344 MB | +224 MB | ▼120 MB (-35%) |
| SKS_SimplePostIndustry_ver1.zip | 36 MB | +94 MB（単独実測） | 測定済 | — |
| Simutrans-Tree.zip | 30 MB | +78 MB（単独実測） | 測定済 | — |

全ファイルでOOMなし（512MB制限内）を確認。

---

## ベンチマーク駆動の改善手順（再現手順）

1. **ログ追加**: `ReparsePakFilesCommand` にピークメモリログを仕込む
2. **全体スキャン**: `php artisan attachment:reparse-pak-files --max-size=0 --sync` で全件処理
3. **ワースト抽出**: ログから `peak_delta_mb` の大きいファイルIDを3件程度ピックアップ
4. **単独ベースライン測定**: `php artisan attachment:reparse-pak-files {id} --sync` で各ファイルを単独実行し実際の消費量を記録
5. **根本原因分析**: メモリ確保のタイミング（パース中 vs. 抽出中）を追跡
6. **改修・再測定**: コード変更後、同じ3ファイルを単独実行して効果確認
7. **全体再スキャン**: 全件処理で副作用がないことを確認

> `memory_get_peak_usage(true)` は単調増加なので、**全体scan中のデルタは過小評価になる**。
> 正確な比較には単独実行が必要。

---

## 関連ファイル

- `app/Services/FileInfo/Extractors/Pak/Node.php` — 遅延データ生成
- `app/Services/FileInfo/Extractors/Pak/BinaryReader.php` — getBinary()追加
- `app/Services/FileInfo/Extractors/Pak/PakParser.php` — 早期unset
- `app/Services/FileInfo/FileInfoService.php` — ZIPエントリ早期解放
- `app/Console/Commands/Attachment/ReparsePakFilesCommand.php` — ピークメモリログ
- `app/Services/FileInfo/ZipArchiveParser.php` — バイナリ判定ホワイトリスト化

## 関連コミット

- `d7d8eeec` — fix: ZipArchiveParserのバイナリ判定をテキスト拡張子ホワイトリスト方式に変更
- `ff08ecdd` — perf: Nodeのデータを遅延生成に変更しpakパース時のメモリ消費を削減
