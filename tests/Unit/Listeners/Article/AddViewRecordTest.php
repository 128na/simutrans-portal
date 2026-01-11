<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\ArticleShown;
use App\Listeners\Article\AddViewRecord;
use App\Models\Article;
use App\Repositories\Article\ViewCountRepository;
use Mockery;
use Tests\TestCase;

class AddViewRecordTest extends TestCase
{
    public function test_handle_calls_count_up(): void
    {
        // Arrange
        $article = Article::factory()->make(['id' => 1]);
        $event = new ArticleShown($article);

        $viewCountRepository = Mockery::mock(ViewCountRepository::class);
        $viewCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article);

        $listener = new AddViewRecord($viewCountRepository);

        // Act
        $listener->handle($event);

        // Assert - モックの期待値が満たされることを確認
        $this->assertTrue(true);
    }

    public function test_handle_with_different_articles(): void
    {
        // Arrange
        $article1 = Article::factory()->make(['id' => 1]);
        $article2 = Article::factory()->make(['id' => 2]);
        $event1 = new ArticleShown($article1);
        $event2 = new ArticleShown($article2);

        $viewCountRepository = Mockery::mock(ViewCountRepository::class);
        $viewCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article1);
        $viewCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article2);

        $listener = new AddViewRecord($viewCountRepository);

        // Act
        $listener->handle($event1);
        $listener->handle($event2);

        // Assert
        $this->assertTrue(true);
    }
}
