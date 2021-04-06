<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Closure;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaginateByAdvancedSearchTest extends TestCase
{
    private ArticleRepository $repository;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        Article::query()->delete();
        $this->article = Article::factory()->create(['status' => 'publish']);
        Tag::factory()->count(50)->create();
        User::factory()->count(50)->create();
    }

    /**
     * @dataProvider dataField
     * @dataProvider dataCond
     * @dataProvider dataStatus
     * @dataProvider dataPostType
     * @dataProvider dataCategory
     * @dataProvider dataTag
     * @dataProvider dataUser
     * @dataProvider dataDate
     */
    public function test(Closure $fn, int $expectedCount)
    {
        $cond = Closure::bind($fn, $this)();
        $res = $this->repository->paginateByAdvancedSearch(
            $cond['word'] ?? null,
            $cond['categories'] ?? null,
            $cond['categoryAnd'] ?? true,
            $cond['tags'] ?? null,
            $cond['tagAnd'] ?? true,
            $cond['users'] ?? null,
            $cond['userAnd'] ?? true,
            $cond['startAt'] ?? null,
            $cond['endAt'] ?? null
        );

        $this->assertEquals($expectedCount, $res->total(), '検索結果が一致すること');
    }

    public function dataField()
    {
        // 検索フィールド
        yield 'フィールド タイトル' => [function () {
            return ['word' => $this->article->title];
        }, 1];
        yield 'フィールド コンテンツ' => [function () {
            $this->article->update([
                'post_type' => 'addon-introduction',
                'contents' => ['description' => 'foo bar'],
            ]);

            return ['word' => 'bar'];
        }, 1];
    }

    public function dataCond()
    {
        // AND/OR
        yield 'OR検索' => [function () {
            $this->article->update([
                'post_type' => 'addon-introduction',
                'title' => 'foo bar',
            ]);

            return ['word' => 'foo or baz'];
        }, 1];
        yield 'AND検索' => [function () {
            $this->article->update([
                'post_type' => 'addon-introduction',
                'title' => 'foo bar',
            ]);

            return ['word' => 'foo baz'];
        }, 0];
    }

    public function dataStatus()
    {
        // 公開ステータス
        yield 'ステータス ゴミ箱' => [function () {
            $this->article->update([
                'status' => 'trash',
            ]);

            return ['word' => $this->article->title];
        }, 0];
        yield 'ステータス 非公開' => [function () {
            $this->article->update([
                'status' => 'private',
            ]);

            return ['word' => $this->article->title];
        }, 0];
        yield 'ステータス 下書き' => [function () {
            $this->article->update([
                'status' => 'draft',
            ]);

            return ['word' => $this->article->title];
        }, 0];
        yield 'ステータス 公開' => [function () {
            $this->article->update([
                'status' => 'publish',
            ]);

            return ['word' => $this->article->title];
        }, 1];
    }

    public function dataPostType()
    {
        // 投稿形式
        yield '投稿形式 アドオン紹介' => [function () {
            $this->article->update(['post_type' => 'addon-introduction']);

            return ['word' => $this->article->title];
        }, 1];
        yield '投稿形式 アドオン投稿' => [function () {
            $this->article->update(['post_type' => 'addon-post']);

            return ['word' => $this->article->title];
        }, 1];
        yield '投稿形式 一般記事' => [function () {
            $this->article->update(['post_type' => 'page']);

            return ['word' => $this->article->title];
        }, 1];
        yield '投稿形式 マークダウン記事' => [function () {
            $this->article->update(['post_type' => 'markdown']);

            return ['word' => $this->article->title];
        }, 1];
    }

    public function dataCategory()
    {
        // カテゴリ
        yield 'カテゴリ AND 1以上つ含む' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);

            return ['categories' => $categories];
        }, 1];
        yield 'カテゴリ AND 1つも含まない' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);
            $notInCategories = Category::whereNotIn('id', $categories->pluck('id')->all())->limit(2)->get();

            return ['categories' => $notInCategories];
        }, 0];
        yield 'カテゴリ AND 一部含む' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);
            $partialCategories = Category::whereNotIn('id', $categories->pluck('id')->all())
                ->limit(1)->get()->merge([$categories->first()]);

            return ['categories' => $partialCategories];
        }, 0];
        yield 'カテゴリ OR 1以上つ含む' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);
            $categories = $categories->merge(Category::inRandomOrder()->limit(2)->get());

            return ['categories' => $categories, 'categoryAnd' => false];
        }, 1];
        yield 'カテゴリ OR 1つも含まない' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);
            $notInCategories = Category::whereNotIn('id', $categories->pluck('id')->all())->limit(2)->get();

            return ['categories' => $notInCategories, 'categoryAnd' => false];
        }, 0];
        yield 'カテゴリ OR 一部含む' => [function () {
            $categories = Category::inRandomOrder()->limit(2)->get();
            $this->article->categories()->sync($categories);
            $partialCategories = Category::whereNotIn('id', $categories->pluck('id')->all())
                ->limit(1)->get()->merge([$categories->first()]);

            return ['categories' => $partialCategories, 'categoryAnd' => false];
        }, 1];
    }

    public function dataTag()
    {
        // タグ
        yield 'タグ AND 1以上つ含む' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);

            return ['tags' => $tags];
        }, 1];
        yield 'タグ AND 1つも含まない' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);
            $notInTags = Tag::whereNotIn('id', $tags->pluck('id')->all())->limit(2)->get();

            return ['tags' => $notInTags];
        }, 0];
        yield 'タグ AND 一部含む' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);
            $partialTags = Tag::whereNotIn('id', $tags->pluck('id')->all())
                ->limit(1)->get()->merge([$tags->first()]);

            return ['tags' => $partialTags];
        }, 0];
        yield 'タグ OR 1以上つ含む' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);
            $tags = $tags->merge(Tag::inRandomOrder()->limit(2)->get());

            return ['tags' => $tags, 'tagAnd' => false];
        }, 1];
        yield 'タグ OR 1つも含まない' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);
            $notInTags = Tag::whereNotIn('id', $tags->pluck('id')->all())->limit(2)->get();

            return ['tags' => $notInTags, 'tagAnd' => false];
        }, 0];
        yield 'タグ OR 一部含む' => [function () {
            $tags = Tag::inRandomOrder()->limit(2)->get();
            $this->article->tags()->sync($tags);
            $partialTags = Tag::whereNotIn('id', $tags->pluck('id')->all())
                ->limit(1)->get()->merge([$tags->first()]);

            return ['tags' => $partialTags, 'tagAnd' => false];
        }, 1];
    }

    public function dataUser()
    {
        // ユーザー
        yield 'ユーザー AND 1以上つ含む' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);

            return ['users' => Collect([$user])];
        }, 1];
        yield 'ユーザー AND 1つも含まない' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);
            $notInUsers = User::whereNotIn('id', [$user->id])->limit(2)->get();

            return ['users' => $notInUsers];
        }, 0];
        yield 'ユーザー AND 一部含む' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);
            $partialUsers = User::whereNotIn('id', [$user->id])
                ->limit(1)->get()->merge([$user]);

            return ['users' => $partialUsers];
        }, 0];
        yield 'ユーザー OR 1以上つ含む' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);
            $users = collect([$user])->merge([User::inRandomOrder()->first()]);

            return ['users' => $users, 'userAnd' => false];
        }, 1];
        yield 'ユーザー OR 1つも含まない' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);
            $notInUsers = User::whereNotIn('id', [$user->id])->limit(2)->get();

            return ['users' => $notInUsers, 'userAnd' => false];
        }, 0];
        yield 'ユーザー OR 一部含む' => [function () {
            $user = User::inRandomOrder()->first();
            $user->articles()->save($this->article);
            $partialUsers = User::whereNotIn('id', [$user->id])
                ->limit(1)->get()->merge([$user]);

            return ['users' => $partialUsers, 'userAnd' => false];
        }, 1];
    }

    public function dataDate()
    {
        yield '開始日 より前' => [function () {
            return ['startAt' => today()->modify('1 day')];
        }, 0];
        yield '開始日' => [function () {
            return ['startAt' => today()];
        }, 1];
        yield '開始日 より後' => [function () {
            return ['startAt' => today()->modify('-1 day')];
        }, 1];
        yield '終了日 より前' => [function () {
            return ['endAt' => today()->modify('1 day')];
        }, 1];
        yield '終了日' => [function () {
            return ['endAt' => today()];
        }, 1];
        yield '終了日 より後' => [function () {
            return ['endAt' => today()->modify('-1 day')];
        }, 0];
    }

    /**
     * @dataProvider dataOrder
     */
    public function testOrder(string $order, string $direction, bool $article1IsFirst)
    {
        $article1 = $this->article;
        DB::update('update articles set created_at = ? where id = ?', [
            today()->yesterday()->toDateTimeString(),
            $article1->id,
        ]);
        $article2 = Article::factory()->create(['status' => 'publish']);
        $res = $this->repository->paginateByAdvancedSearch(
            null, null, null, null, null, null, null, null, null, $order, $direction
        );

        $this->assertEquals(2, $res->total(), '検索結果が一致すること');
        $itr = $res->getIterator();
        $first = $itr->current();
        $itr->next();
        $second = $itr->current();

        if ($article1IsFirst) {
            $this->assertEquals($article1->id, $first->id, 'Article1 が一番目に来ること');
            $this->assertEquals($article2->id, $second->id, 'Article2 が二番目に来ること');
        } else {
            $this->assertEquals($article2->id, $first->id, 'Article2 が一番目に来ること');
            $this->assertEquals($article1->id, $second->id, 'Article1 が二番目に来ること');
        }
    }

    public function dataOrder()
    {
        yield '作成日時 昇順' => [
            'created_at', 'desc', false,
        ];
        yield '作成日時 降順' => [
            'created_at', 'asc', true,
        ];
    }

    /**
     * @dataProvider dataLimit
     */
    public function testLimit(int $limit)
    {
        Article::factory()->count(49)->create();
        $res = $this->repository->paginateByAdvancedSearch(
            null, null, null, null, null, null, null, null, null, 'updated_at', 'desc', $limit
        );
        $this->assertEquals($limit, $res->perPage(), '1ページ当たりの取得件数が一致すること');
    }

    public function dataLimit()
    {
        yield '1' => [1];
        yield '100' => [100];
    }
}
