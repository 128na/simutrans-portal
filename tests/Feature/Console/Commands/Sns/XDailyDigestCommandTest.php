<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Sns;

use App\Actions\SendSNS\Article\BuildXDigestText;
use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\Data\XDigestArticles;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Console\Commands\Sns\XDailyDigestCommand;
use App\Enums\XDigestArticleType;
use App\Models\Article;
use App\Models\XDigestLog;
use App\Services\Twitter\TwitterV2Api;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class XDailyDigestCommandTest extends TestCase
{
    public function test_bootstrap_run_initializes_cutoff_without_posting_when_no_log_exists(): void
    {
        $this->mock(GetXDigestArticles::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('__invoke');
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('post');
        });

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $exitCode = $this->artisan('sns:x-daily-digest');

        $exitCode->assertSuccessful();
        $this->assertDatabaseCount('x_digest_logs', 1);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 0,
            'cutoff_at' => $now->toDateTimeString(),
        ]);
    }

    public function test_does_not_post_and_advances_cutoff_when_no_articles_matched(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff, 'article_count' => 0]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($previousCutoff, $now): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn (CarbonImmutable $cutoff): bool => $cutoff->equalTo($previousCutoff)), \Mockery::on(fn (CarbonImmutable $until): bool => $until->equalTo($now)))
                ->andReturn(new XDigestArticles(new Collection, 0));
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('post');
        });

        $exitCode = $this->artisan('sns:x-daily-digest');

        $exitCode->assertSuccessful();
        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 0,
            'cutoff_at' => $now->toDateTimeString(),
        ]);
    }

    public function test_posts_digest_and_advances_cutoff_on_success(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'published_at' => $now->subHour(),
            'modified_at' => $now->subHour(),
        ]);
        $digestArticle = new XDigestArticle($article, XDigestArticleType::Publish, $now->subHour());
        $digest = new XDigestArticles(Collection::make([$digestArticle]), 4);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->andReturn($digest);
        });
        $this->mock(BuildXDigestText::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->with($digest)->andReturn('digest text');
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')
                ->once()
                ->with('tweets', ['text' => 'digest text'])
                ->andReturn(['data' => ['id' => '123']]);
            $mock->expects('getLastHttpCode')->once()->andReturn(201);
        });

        $exitCode = $this->artisan('sns:x-daily-digest');

        $exitCode->assertSuccessful();
        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 4,
            'cutoff_at' => $now->toDateTimeString(),
        ]);
    }

    public function test_does_not_advance_cutoff_and_reports_exception_on_non_2xx_response(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'published_at' => $now->subHour(),
            'modified_at' => $now->subHour(),
        ]);
        $digestArticle = new XDigestArticle($article, XDigestArticleType::Publish, $now->subHour());
        $digest = new XDigestArticles(Collection::make([$digestArticle]), 1);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->andReturn($digest);
        });
        $this->mock(BuildXDigestText::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')->once()->andReturn('digest text');
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')->once()->andReturn(['errors' => [['message' => 'Unauthorized']]]);
            $mock->expects('getLastHttpCode')->once()->andReturn(401);
        });

        $exitCode = $this->artisan('sns:x-daily-digest');

        $exitCode->assertFailed();
        // cutoffは進まない(前回の1件のみ)。次回実行で同じ範囲を再試行できる。
        $this->assertDatabaseCount('x_digest_logs', 1);
        $this->assertDatabaseHas('x_digest_logs', ['cutoff_at' => $previousCutoff->toDateTimeString()]);
    }

    public function test_does_not_advance_cutoff_when_the_api_call_throws(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'published_at' => $now->subHour(),
            'modified_at' => $now->subHour(),
        ]);
        $digestArticle = new XDigestArticle($article, XDigestArticleType::Publish, $now->subHour());
        $digest = new XDigestArticles(Collection::make([$digestArticle]), 1);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->andReturn($digest);
        });
        $this->mock(BuildXDigestText::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')->once()->andReturn('digest text');
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->expects('post')->once()->andThrow(new \Exception('network error'));
        });

        $exitCode = $this->artisan('sns:x-daily-digest');

        $exitCode->assertFailed();
        $this->assertDatabaseCount('x_digest_logs', 1);
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(XDailyDigestCommand::class);

        $this->assertSame('sns:x-daily-digest', $command->getName());
    }
}
