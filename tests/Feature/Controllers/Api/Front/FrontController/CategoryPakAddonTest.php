<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use App\Models\Category;
use Tests\Feature\TestCase;

class CategoryPakAddonTest extends TestCase
{
    private Article $article;

    private Category $pakCategory;

    private Category $addonCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        $this->pakCategory = Category::pak()->inRandomOrder()->firstOrFail();
        $this->addonCategory = Category::addon()->inRandomOrder()->firstOrFail();
        $this->article->categories()->saveMany([$this->pakCategory, $this->addonCategory]);
    }

    public function test(): void
    {
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/%s',
            $this->pakCategory->slug,
            $this->addonCategory->slug
        ));

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test存在しないPak(): void
    {
        $testResponse = $this->get('api/front/categories/pak/missing/'.$this->addonCategory->slug);

        $testResponse->assertNotFound();
    }

    public function test存在しないAddon(): void
    {
        $testResponse = $this->get(sprintf('api/front/categories/pak/%s/missing', $this->pakCategory->slug));

        $testResponse->assertNotFound();
    }
}
