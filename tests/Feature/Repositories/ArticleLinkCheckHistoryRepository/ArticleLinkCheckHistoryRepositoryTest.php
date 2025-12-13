<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleLinkCheckHistoryRepository;

use App\Models\Article;
use App\Models\ArticleLinkCheckHistory;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use Tests\Feature\TestCase;

class ArticleLinkCheckHistoryRepositoryTest extends TestCase
{
    private ArticleLinkCheckHistoryRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleLinkCheckHistoryRepository::class);
    }

    public function test_get_returns_zero_when_no_history(): void
    {
        $article = Article::factory()->create();

        $result = $this->repository->get($article);

        $this->assertSame(0, $result);
    }

    public function test_get_returns_failed_count_when_history_exists(): void
    {
        $article = Article::factory()->create();
        ArticleLinkCheckHistory::factory()->create([
            'article_id' => $article->id,
            'failed_count' => 5,
        ]);

        $result = $this->repository->get($article);

        $this->assertSame(5, $result);
    }

    public function test_increment_creates_new_history(): void
    {
        $article = Article::factory()->create();

        $this->repository->increment($article);

        $this->assertDatabaseHas('article_link_check_histories', [
            'article_id' => $article->id,
            'failed_count' => 1,
        ]);

        $history = ArticleLinkCheckHistory::where('article_id', $article->id)->first();
        $this->assertNotNull($history);
        $this->assertInstanceOf(\DateTimeInterface::class, $history->last_checked_at);
    }

    public function test_increment_increments_existing_history(): void
    {
        $article = Article::factory()->create();
        ArticleLinkCheckHistory::factory()->create([
            'article_id' => $article->id,
            'failed_count' => 3,
        ]);

        $this->repository->increment($article);

        $this->assertDatabaseHas('article_link_check_histories', [
            'article_id' => $article->id,
            'failed_count' => 4,
        ]);
    }

    public function test_increment_updates_last_checked_at(): void
    {
        $article = Article::factory()->create();
        $oldTimestamp = now()->subHours(2);
        ArticleLinkCheckHistory::factory()->create([
            'article_id' => $article->id,
            'failed_count' => 1,
            'last_checked_at' => $oldTimestamp,
        ]);

        $this->repository->increment($article);

        $history = ArticleLinkCheckHistory::where('article_id', $article->id)->first();
        $this->assertNotNull($history);
        $this->assertTrue($history->last_checked_at->greaterThan($oldTimestamp));
    }

    public function test_clear_deletes_history(): void
    {
        $article = Article::factory()->create();
        ArticleLinkCheckHistory::factory()->create([
            'article_id' => $article->id,
            'failed_count' => 10,
        ]);

        $this->repository->clear($article);

        $this->assertDatabaseMissing('article_link_check_histories', [
            'article_id' => $article->id,
        ]);
    }

    public function test_clear_handles_non_existent_history(): void
    {
        $article = Article::factory()->create();

        // Should not throw an exception
        $this->repository->clear($article);

        $this->assertDatabaseMissing('article_link_check_histories', [
            'article_id' => $article->id,
        ]);
    }

    public function test_increment_multiple_times(): void
    {
        $article = Article::factory()->create();

        $this->repository->increment($article);
        $this->repository->increment($article);
        $this->repository->increment($article);

        $this->assertDatabaseHas('article_link_check_histories', [
            'article_id' => $article->id,
            'failed_count' => 3,
        ]);
    }

    public function test_get_increment_clear_workflow(): void
    {
        $article = Article::factory()->create();

        // Initial state: no history
        $this->assertSame(0, $this->repository->get($article));

        // After increment: count is 1
        $this->repository->increment($article);
        $this->assertSame(1, $this->repository->get($article));

        // After another increment: count is 2
        $this->repository->increment($article);
        $this->assertSame(2, $this->repository->get($article));

        // After clear: count is back to 0
        $this->repository->clear($article);
        $this->assertSame(0, $this->repository->get($article));
    }
}
