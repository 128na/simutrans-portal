````markdown
# Services と Actions の配置判断フローチャート

新しいクラスを作成する際に、`Services/` と `Actions/` のどちらに配置するかを判断するためのフローチャートです。

## フローチャート

```
┌─────────────────────────────────────────────────────────────┐
│           新しいクラスを作成する必要がある                      │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
         ┌─────────────────────────────────────┐
         │  外部APIやインフラと通信する？        │
         │  (例: Twitter, Discord, FileSystem) │
         └────────┬────────────────────┬────────┘
                  │ Yes                │ No
                  ▼                    ▼
         ┌──────────────────┐  ┌──────────────────────┐
         │  Services/       │  │  複数のドメインで      │
         │  ExternalApi/    │  │  再利用される？        │
         │  または           │  └────────┬─────────────┘
         │  Infrastructure/ │           │ Yes     │ No
         └──────────────────┘           ▼         ▼
                              ┌──────────────┐  ┌─────────────────┐
                              │  Services/   │  │  特定のユース    │
                              │  Utility/    │  │  ケースを表現    │
                              └──────────────┘  │  する？          │
                                                └────┬────────────┘
                                                     │ Yes   │ No
                                                     ▼       ▼
                                            ┌──────────────┐  ┌──────────┐
                                            │  Actions/    │  │  既存の  │
                                            │  {Domain}/   │  │  パターン │
                                            └──────────────┘  │  を再検討 │
                                                              └──────────┘
```

## 判断基準の詳細

### 1️⃣ 外部APIやインフラと通信する？

**Yes の場合 → `Services/ExternalApi/` または `Services/Infrastructure/`**

**例:**

- Twitter API 呼び出し → `Services/Twitter/TwitterV2Api`
- Discord API 呼び出し → `Services/Discord/InviteService`
- ファイルシステム操作 → `Services/FileInfo/FileInfoService`
- メール送信 → `Services/Mail/MailService`

**特徴:**

- 外部システムとの境界
- 技術的な詳細を隠蔽
- テストでモック化される

---

### 2️⃣ 複数のドメインで再利用される？

**Yes の場合 → `Services/Utility/`**

**例:**

- Markdown変換 → `Services/MarkdownService`
  - 記事、コメント、プロフィールなど複数箇所で利用
- Feed生成 → `Services/FeedService`
  - 複数のコンテンツタイプで利用
- 日付フォーマット → `Services/DateFormatterService`
  - アプリケーション全体で利用

**特徴:**

- ドメイン非依存
- 汎用的なユーティリティ
- ビジネスロジックを含まない

---

### 3️⃣ 特定のユースケースを表現する？

**Yes の場合 → `Actions/{Domain}/`**

**例:**

- 記事作成 → `Actions/Article/StoreArticle`
- ユーザー登録 → `Actions/User/Registration`
- アナリティクス取得 → `Actions/Analytics/FindArticles`
- SNS投稿 → `Actions/SendSNS/Article/ToTwitter`

**特徴:**

- 1クラス = 1ユースケース
- ビジネスルールを含む
- 複数のRepository/Serviceを組み合わせる
- コントローラーから直接呼び出される

---

## クイックチェックリスト

新しいクラスを作成する前に、以下の質問に答えてください：

### Services に配置する場合

- [ ] 外部APIと通信する？
- [ ] ファイルシステムやキャッシュなどのインフラと連携する？
- [ ] 複数のドメインから利用される汎用的な機能？
- [ ] ビジネスルールを含まない？
- [ ] ステートレスである？

**3つ以上 Yes → Services に配置**

### Actions に配置する場合

- [ ] 特定のユースケース（例: 記事作成、ユーザー登録）を表現する？
- [ ] ビジネスルール・ドメインロジックを含む？
- [ ] 複数のRepository/Serviceを組み合わせる？
- [ ] コントローラーから直接呼び出される？
- [ ] 単一責任（1つのメソッド）である？

**3つ以上 Yes → Actions に配置**

---

## 実例で学ぶ

### 実例1: Markdown変換

**質問**: Markdown → HTML 変換クラスをどこに配置する？

**分析**:

- 外部ライブラリ（cebe/markdown）を使用 ✓
- 記事、コメント、プロフィールなど複数箇所で利用 ✓
- ビジネスルールなし ✓
- ステートレス ✓

**結論**: `Services/MarkdownService` ✅

---

### 実例2: 記事作成

**質問**: 記事作成ロジックをどこに配置する？

**分析**:

- 特定のユースケース（記事を作成する） ✓
- ビジネスルール（公開日時の決定、権限チェック等）を含む ✓
- Repository（記事保存）と Service（Markdown変換）を組み合わせる ✓
- コントローラーから呼び出される ✓

**結論**: `Actions/Article/StoreArticle` ✅

---

### 実例3: Twitter API クライアント

**質問**: Twitter投稿機能をどこに配置する？

**分析**:

- 外部API（Twitter）と通信 ✓
- 技術的な詳細を隠蔽 ✓
- 複数のユースケースから利用される可能性 ✓
- ビジネスルールなし ✓

**結論**: `Services/Twitter/TwitterV2Api` ✅

---

### 実例4: アナリティクスデータ取得

**質問**: アナリティクスの記事検索をどこに配置する？

**分析**:

- 特定のユースケース（アナリティクス用の記事取得） ✓
- ビジネスルール（期間の計算、権限チェック等）を含む ✓
- Repository を利用 ✓
- アナリティクス専用 ✓

**結論**: `Actions/Analytics/FindArticles` ✅

---

## よくある質問

### Q1: Service と Action の両方に当てはまる場合は？

**A**: ドメイン固有性を基準に判断します。

- **複数のドメインで使える汎用性** → Services
- **特定のドメイン/ユースケース専用** → Actions

例: `DecidePublishedAt` は記事ドメイン専用のロジックなので `Actions/Article/DecidePublishedAt`

---

### Q2: Controller に直接ロジックを書いてもいい？

**A**: 薄いコントローラーを保つため、以下のルールを守ってください：

- **単純なCRUD** → Repository を直接利用してもOK
- **ビジネスロジックがある** → Action を作成
- **外部API連携** → Service を作成

---

### Q3: Service から別の Service を呼んでもいい？

**A**: 可能ですが、以下に注意：

- **依存が深くなりすぎない** ように
- **循環参照を避ける**
- 多数のServiceを組み合わせる場合は Action を検討

---

### Q4: Action から別の Action を呼んでもいい？

**A**: 可能です。実際に既存コードでも使われています。

例:

```php
// Actions/Article/StoreArticle.php
public function __invoke(User $user, array $data): Article
{
    // 別のActionを利用
    $publishedAt = ($this->decidePublishedAt)($data['published_at'], $data['status']);
    ($this->syncRelatedModels)($article, $data);
}
```

---

### Q5: 既存コードを移動する必要はある？

**A**: **原則として移動は不要です**。

- 既存コードは概ね適切に配置されている
- 新しいコードは本ガイドラインに従う
- 大規模なリファクタリングはリスクが高い

ただし、明らかに配置が不適切な場合は個別に検討してください。

---

## まとめ

### 覚えておくべき3つのポイント

1. **Services = 技術的な関心事**
   - 外部API、インフラ、汎用ユーティリティ

2. **Actions = ビジネスの関心事**
   - 1クラス = 1ユースケース

3. **判断基準は明確**
   - 「複数のドメインで使う？」→ Services
   - 「特定のユースケース？」→ Actions

---

**関連ドキュメント**: [architecture-services-actions-20260103-knowledge.md](./architecture-services-actions-20260103-knowledge.md)

**最終更新**: 2025-11-24
````
