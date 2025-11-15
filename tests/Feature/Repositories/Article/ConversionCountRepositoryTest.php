<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\Article;

use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\ConversionCountRepository;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

final class ConversionCountRepositoryTest extends TestCase
{
    private ConversionCountRepository $conversionCountRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->conversionCountRepository = app(ConversionCountRepository::class);
    }

    public function test_count_up(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->create();

        $this->conversionCountRepository->countUp($article);

        $counts = DB::table('conversion_counts')
            ->where('article_id', $article->id)
            ->get();

        $this->assertCount(4, $counts);
        $this->assertNotEmpty($counts->where('type', 1)); // daily
        $this->assertNotEmpty($counts->where('type', 2)); // monthly
        $this->assertNotEmpty($counts->where('type', 3)); // yearly
        $this->assertNotEmpty($counts->where('type', 4)); // total
    }
}
