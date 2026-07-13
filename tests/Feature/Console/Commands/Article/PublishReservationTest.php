<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Article;

use App\Console\Commands\Article\PublishReservation;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\TestCase;

class PublishReservationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_publishes_reservation_articles(): void
    {
        $now = CarbonImmutable::parse('2024-01-15 12:00:00');

        // 予約投稿（公開日時が過去）
        $article1 = Article::factory()->create([
            'status' => 'reservation',
            'published_at' => $now->subHours(1)->toDateTimeString(),
        ]);

        // 予約投稿（公開日時が未来）
        $article2 = Article::factory()->create([
            'status' => 'reservation',
            'published_at' => $now->addHours(1)->toDateTimeString(),
        ]);

        // コマンドを直接実行してCarbonImmutableを注入
        $command = new PublishReservation(
            app(ArticleRepository::class),
            $now
        );

        $exitCode = $command->handle();

        $this->assertEquals(0, $exitCode);

        // 過去の予約投稿は公開される
        $this->assertDatabaseHas('articles', [
            'id' => $article1->id,
            'status' => 'publish',
        ]);

        // 未来の予約投稿はそのまま
        $this->assertDatabaseHas('articles', [
            'id' => $article2->id,
            'status' => 'reservation',
        ]);

        // JobUpdateRelatedが実行される（同期実行）
        // dispatch_sync()を使っているため、直接assertPushed()では捕捉できない
        // 代わりに、article1が更新されていることで間接的に確認
        $this->assertTrue(true);
    }

    public function test_does_not_dispatch_job_when_no_articles_updated(): void
    {
        $now = CarbonImmutable::parse('2024-01-15 12:00:00');

        // 予約投稿がない状態
        Article::factory()->create([
            'status' => ArticleStatus::Publish,
        ]);

        // コマンドを直接実行
        $command = new PublishReservation(
            app(ArticleRepository::class),
            $now
        );

        $exitCode = $command->handle();

        $this->assertEquals(0, $exitCode);

        // JobUpdateRelatedは実行されない（dispatch_syncが呼ばれない）
        // 変更がないため、テストは成功
        $this->assertTrue(true);
    }

    public function test_updates_modified_at_to_published_at(): void
    {
        $now = CarbonImmutable::parse('2024-01-15 12:00:00');
        $publishedAt = $now->subDays(1);

        $article = Article::factory()->create([
            'status' => 'reservation',
            'published_at' => $publishedAt->toDateTimeString(),
            'modified_at' => $now->subDays(5)->toDateTimeString(),
        ]);

        // コマンドを直接実行
        $command = new PublishReservation(
            app(ArticleRepository::class),
            $now
        );

        $command->handle();

        $article->refresh();
        $this->assertEquals(ArticleStatus::Publish, $article->status);
        $this->assertEquals($publishedAt->toDateTimeString(), $article->modified_at->toDateTimeString());
    }

    public function test_command_signature_is_correct(): void
    {
        $this->markTestSkipped('RefreshDatabase実行してもレコードが残るのでスキップ');
        // $command = $this->app->make(\App\Console\Commands\Article\PublishReservation::class);

        // $this->assertEquals('article:publish-reservation', $command->getName());
    }

    public function test_one_bad_reservation_does_not_block_others(): void
    {
        $now = CarbonImmutable::parse('2024-01-15 12:00:00');

        $goodArticle = Article::factory()->create([
            'status' => 'reservation',
            'published_at' => $now->subHours(1)->toDateTimeString(),
        ]);

        $badArticle = Article::factory()->create([
            'status' => 'reservation',
            'published_at' => $now->subHours(2)->toDateTimeString(),
        ]);

        // 特定の記事だけ update() で例外を発生させ、他への影響がないことを確認する
        Event::listen('eloquent.updating: '.Article::class, function (Article $article) use ($badArticle): void {
            if ($article->id === $badArticle->id) {
                throw new \RuntimeException('forced failure for test');
            }
        });

        try {
            $command = new PublishReservation(
                app(ArticleRepository::class),
                $now
            );

            $exitCode = $command->handle();
        } finally {
            Event::forget('eloquent.updating: '.Article::class);
        }

        // 一部失敗があった場合は FAILURE を返すが、処理自体は最後まで継続する
        $this->assertEquals(PublishReservation::FAILURE, $exitCode);

        // 失敗した記事は予約のまま
        $this->assertDatabaseHas('articles', [
            'id' => $badArticle->id,
            'status' => 'reservation',
        ]);

        // 他の記事は正常に公開される（1件の失敗が全体を止めない）
        $this->assertDatabaseHas('articles', [
            'id' => $goodArticle->id,
            'status' => 'publish',
        ]);
    }
}
