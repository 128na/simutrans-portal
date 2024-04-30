<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\CategoryRepository;

use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Tests\Feature\TestCase;

final class FindAllByUserTest extends TestCase
{
    private CategoryRepository $categoryRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = app(CategoryRepository::class);
    }

    public function test_通常ユーザーは管理者用カテゴリを含まない(): void
    {
        $user = User::factory()->create();

        $result = $this->categoryRepository->findAllByUser($user);

        $this->assertTrue($result->every(fn (Category $category): bool => $category->need_admin === false));
    }

    public function test_管理者は管理者用カテゴリも含む(): void
    {
        $user = User::factory()->admin()->create();

        $result = $this->categoryRepository->findAllByUser($user);

        $this->assertTrue($result->some(fn (Category $category): bool => $category->need_admin === true));
    }
}
