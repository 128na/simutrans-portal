<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\Article;

use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\ViewCountRepository;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

final class ViewCountRepositoryTest extends TestCase
{
    private ViewCountRepository $viewCountRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewCountRepository = app(ViewCountRepository::class);
    }

    public function test_count_up(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        $this->viewCountRepository->countUp($article);

        $counts = DB::table('view_counts')
            ->where('article_id', $article->id)
            ->get();

        $this->assertCount(4, $counts);
        $this->assertNotEmpty($counts->where('type', 1)); // daily
        $this->assertNotEmpty($counts->where('type', 2)); // monthly
        $this->assertNotEmpty($counts->where('type', 3)); // yearly
        $this->assertNotEmpty($counts->where('type', 4)); // total
    }
}
