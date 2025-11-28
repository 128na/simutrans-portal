# Test Pak Files Generator

このディレクトリには、テスト用のpakファイルを一括生成するためのスクリプトとmakeobjバイナリが含まれています。

## ディレクトリ構造

```
makeobjs/
├── run-makeobj.bat          # 一括生成スクリプト（Windows）
├── makeobj-60.8.exe         # Simutrans 124.3.1+
├── makeobj-60.exe           # Simutrans 120.2+
├── makeobj-55.4.exe         # Simutrans 120.1+
├── makeobj-50.exe           # Simutrans 102.1+
├── makeobj-48.exe           # Simutrans 99.17+
└── README.md                # このファイル
```

## 使用方法

### Windows

```cmd
cd tests\Unit\Services\FileInfo\Extractors\file\makeobjs
.\run-makeobj.bat
```

スクリプトは以下の処理を自動実行します：

1. ソースファイルのコピー（`test.dat`, `test_transparent.dat`, `vehicle.dat`, 画像ファイル）
2. 各makeobjバージョンでpakファイルを生成
3. 一時ファイルのクリーンアップ

## 生成されるファイル

スクリプト実行後、`../`（`file/`ディレクトリ）に以下のファイルが生成されます：

### Way Objects（道路）

- `test-48.pak` - makeobj 48で生成
- `test-50.pak` - makeobj 50で生成
- `test-55.4.pak` - makeobj 55.4で生成
- `test-60.pak` - makeobj 60で生成
- `way.test_1.pak` - makeobj 60.8で生成（個別ファイル）

### Way Objects with Transparency（透過画像付き道路）

- `test_transparent-48.pak` - makeobj 48で生成
- `test_transparent-50.pak` - makeobj 50で生成
- `test_transparent-55.4.pak` - makeobj 55.4で生成
- `test_transparent-60.pak` - makeobj 60で生成

## ソースファイル

### test.dat

```dat
obj=way
name=test_1
copyright=128Na
waytype=road
topspeed=50
cost=100
maintenance=10
intro_year=1900
Image[NS][0]=test.0.0
Image[EW][0]=test.0.0
```

## makeobjバージョンの違い

各makeobjバージョンはSimutransの特定のバージョンに対応しており、生成されるpakファイルのフォーマットが異なります：

| makeobj | Simutrans Version | 主な変更点                         |
| ------- | ----------------- | ---------------------------------- |
| 60.8    | 124.3.1+          | 個別ファイル出力、最新フォーマット |
| 60      | 120.2+            |                                    |
| 55.4    | 120.1+            |                                    |
| 50      | 102.1+            |                                    |
| 48      | 99.17+            | 最古のサポートバージョン           |

## テストでの使用

生成されたpakファイルは `PakParserTest.php` で使用されます：

```php
// tests/Unit/Services/FileInfo/Extractors/Pak/PakParserTest.php
public static function makeobjVersionProvider(): array
{
    return [
        'makeobj-48' => ['pakFile' => 'test-48.pak', 'expectedName' => 'test_1'],
        'makeobj-50' => ['pakFile' => 'test-50.pak', 'expectedName' => 'test_1'],
        'makeobj-55.4' => ['pakFile' => 'test-55.4.pak', 'expectedName' => 'test_1'],
        'makeobj-60' => ['pakFile' => 'test-60.pak', 'expectedName' => 'test_1'],
        'makeobj-60.8' => ['pakFile' => 'test-60.8.pak', 'expectedName' => 'test_1'],
        // ...
    ];
}
```

## トラブルシューティング

### makeobjが見つからない

makeobjバイナリがこのディレクトリに存在することを確認してください。

### 生成に失敗する

1. ソースファイル（`test.dat`, `vehicle.dat`, 画像ファイル）が`../`に存在するか確認
2. 書き込み権限があるか確認
3. makeobjのバージョンが正しいか確認

### 古いpakファイルが残っている

スクリプトは既存のpakファイルを上書きします。手動で削除する場合：

```cmd
cd tests\Unit\Services\FileInfo\Extractors\file
del *.pak
```

## 注意事項

- makeobjバイナリはGitリポジトリに含まれていません（.gitignoreで除外）
- pakファイルはテスト用途のみに使用してください
- 本番環境では使用しないでください

## 参考

- [Simutrans Wiki - Makeobj](https://simutrans-germany.com/wiki/wiki/en_Makeobj)
- [Simutrans Forum](https://forum.simutrans.com/)
