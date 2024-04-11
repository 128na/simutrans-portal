<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Category;
use Tests\Feature\TestCase;

final class CategoryTest extends TestCase
{
    private Article $article;

    private Category $category;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        $this->category = Category::inRandomOrder()->firstOrFail();
        $this->article->categories()->save($this->category);
    }

    public function test(): void
    {
        $testResponse = $this->get(sprintf('api/front/categories/%s/%s',
            $this->category->type->value,
            $this->category->slug
        ));

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test存在しないtype(): void
    {
        $testResponse = $this->get('api/front/categories/missing/'.$this->category->slug);

        $testResponse->assertNotFound();
    }

    public function test存在しないslug(): void
    {
        $testResponse = $this->get(sprintf('api/front/categories/%s/missing', $this->category->type->value));

        $testResponse->assertNotFound();
    }
}
