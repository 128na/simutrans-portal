<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Category;
use Tests\Feature\TestCase;

final class CategoryPakNoneAddonTest extends TestCase
{
    private Article $article;

    private Category $pakCategory;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        $this->pakCategory = Category::pak()->inRandomOrder()->firstOrFail();
        $this->article->categories()->save($this->pakCategory);
    }

    public function test(): void
    {
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/none', $this->pakCategory->slug));

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test存在しない_pak(): void
    {
        $testResponse = $this->get('api/front/categories/pak/missing/none');

        $testResponse->assertNotFound();
    }
}
