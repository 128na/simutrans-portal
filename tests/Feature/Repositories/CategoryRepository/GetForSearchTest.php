<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\CategoryRepository;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Tests\Feature\TestCase;

class GetForSearchTest extends TestCase
{
    private CategoryRepository $categoryRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = app(CategoryRepository::class);
    }

    public function test(): void
    {
        $first = Category::factory()->create(['slug' => 's1']);
        $second = Category::factory()->create(['slug' => 's2']);

        $categories = $this->categoryRepository->getForSearch();

        $this->assertGreaterThanOrEqual(2, $categories->count());
        $ids = $categories->pluck('id')->all();
        $this->assertContains($first->id, $ids);
        $this->assertContains($second->id, $ids);
    }
}
