<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\ArticleConversion;
use App\Listeners\Article\AddConversionRecord;
use App\Models\Article;
use App\Repositories\Article\ConversionCountRepository;
use Mockery;
use Tests\Unit\TestCase;

class AddConversionRecordTest extends TestCase
{
    public function test_handle_calls_count_up(): void
    {
        // Arrange
        $article = Article::factory()->make(['id' => 1]);
        $event = new ArticleConversion($article);

        $conversionCountRepository = Mockery::mock(ConversionCountRepository::class);
        $conversionCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article);

        $listener = new AddConversionRecord($conversionCountRepository);

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
        $event1 = new ArticleConversion($article1);
        $event2 = new ArticleConversion($article2);

        $conversionCountRepository = Mockery::mock(ConversionCountRepository::class);
        $conversionCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article1);
        $conversionCountRepository->shouldReceive('countUp')
            ->once()
            ->with($article2);

        $listener = new AddConversionRecord($conversionCountRepository);

        // Act
        $listener->handle($event1);
        $listener->handle($event2);

        // Assert
        $this->assertTrue(true);
    }
}
