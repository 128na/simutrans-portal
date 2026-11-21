<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\Article\FrontArticleRepository;
use Tests\Feature\TestCase;

/**
 * 検索のカテゴリ条件: 同じ種類の中は OR、種類どうしは AND
 */
class SearchCategoryTest extends TestCase
{
    private FrontArticleRepository $articleRepository;

    private User $user;

    private Category $pak128;

    private Category $pak128Japan;

    private Category $pak64;

    private Category $monorail;

    private Category $trains;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->articleRepository = app(FrontArticleRepository::class);
        $this->pak128 = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
        $this->pak128Japan = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $this->pak64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->firstOrFail();
        $this->monorail = Category::where('type', CategoryType::Addon)->where('slug', 'monorail-vehicles')->firstOrFail();
        $this->trains = Category::where('type', CategoryType::Addon)->where('slug', 'trains')->firstOrFail();
    }

    public function test同じ種類を複数選ぶとどれかに一致する記事が出る(): void
    {
        $on128 = $this->createArticle($this->pak128, $this->monorail);
        $on128Japan = $this->createArticle($this->pak128Japan, $this->monorail);
        $this->createArticle($this->pak64, $this->monorail);

        $this->assertSame(
            collect([$on128->id, $on128Japan->id])->sort()->values()->all(),
            $this->searchIds([$this->pak128->id, $this->pak128Japan->id]),
        );
    }

    public function test種類をまたぐときは両方に一致する記事だけが出る(): void
    {
        $this->createArticle($this->pak128, $this->trains);
        $this->createArticle($this->pak64, $this->monorail);
        $on128Japan = $this->createArticle($this->pak128Japan, $this->monorail);

        $this->assertSame(
            [$on128Japan->id],
            $this->searchIds([$this->pak128->id, $this->pak128Japan->id, $this->monorail->id]),
            '128 または 128JP、かつモノレール車両の記事だけが出ること',
        );
    }

    public function test単一のカテゴリ指定は従来どおり絞り込める(): void
    {
        $this->createArticle($this->pak128, $this->trains);
        $monorail = $this->createArticle($this->pak128, $this->monorail);

        $this->assertSame([$monorail->id], $this->searchIds([$this->monorail->id]));
    }

    public function test存在しないカテゴリだけを指定すると0件になる(): void
    {
        $this->createArticle($this->pak128, $this->monorail);

        $this->assertSame([], $this->searchIds([999999]));
    }

    /**
     * @return list<int>
     */
    private function searchIds(array $categoryIds): array
    {
        return $this->articleRepository->search(['categoryIds' => $categoryIds])
            ->getCollection()
            ->pluck('id')
            ->sort()
            ->values()
            ->all();
    }

    private function createArticle(Category $pak, Category $addon): Article
    {
        $article = $this->createAddonIntroduction($this->user);
        $article->categories()->attach([$pak->id, $addon->id]);

        return $article;
    }
}
