<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\CategoryRepository;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

final class FindOrFailByTypeAndSlugTest extends TestCase
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
        $category = Category::query()->inRandomOrder()->first();
        $result = $this->categoryRepository->findOrFailByTypeAndSlug($category->type, $category->slug);

        $this->assertSame($category->id, $result->id);
    }

    public function test_存在しないカテゴリはエラー(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->categoryRepository->findOrFailByTypeAndSlug(CategoryType::Addon, 'missing');
    }
}
