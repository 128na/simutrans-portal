<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Sns;

use App\Actions\SendSNS\Article\BuildXDigestText;
use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\Data\XDigestArticles;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Console\Commands\Sns\XDailyDigestCommand;
use App\Enums\XDigestArticleType;
use App\Enums\XDigestLogStatus;
use App\Models\Article;
use App\Models\XDigestLog;
use App\Repositories\XDigestLogRepository;
use App\Services\Twitter\TwitterV2Api;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class XDailyDigestCommandTest extends TestCase
{
    public function test_bootstrap_run_records_a_success_log_without_posting_when_no_log_exists(): void
    {
        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($now): void {
            // cutoffが無い(初回実行)場合、cutoff===untilで呼ばれ、自然に対象0件へ合流する。
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::on(fn (CarbonImmutable $cutoff): bool => $cutoff->equalTo($now)), \Mockery::on(fn (CarbonImmutable $until): bool => $until->equalTo($now)))
                ->andReturn(new XDigestArticles(new Collection, 0));
        });
        $this->mock(TwitterV2Api::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('post');
        });

        // $this->artisan()はPendingCommandを返し、実際の実行はassertSuccessful()等の
        // チェーン呼び出し完了時点(オブジェクト破棄のタイミング)まで遅延される。変数に
        // 保持したまま後続のDBアサーションを書くと、コマンドがまだ実行されていない状態を
        // 検査してしまうため、必ず1文でチェーンする。
        $this->artisan('sns:x-daily-digest')->assertSuccessful();

        $this->assertDatabaseCount('x_digest_logs', 1);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 0,
            'status' => XDigestLogStatus::Success->value,
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

        $this->artisan('sns:x-daily-digest')->assertSuccessful();

        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 0,
            'status' => XDigestLogStatus::Success->value,
            'cutoff_at' => $now->toDateTimeString(),
        ]);
    }

    public function test_writes_a_pending_log_before_calling_the_api_and_resolves_twitter_v2_api_lazily(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'sns_digest_published_at' => $now->subHour(),
            'sns_digest_updated_at' => null,
        ]);
        $digestArticle = new XDigestArticle($article, XDigestArticleType::Publish, $now->subHour());
        $digest = new XDigestArticles(Collection::make([$digestArticle]), 4);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->andReturn($digest);
        });
        $this->mock(BuildXDigestText::class, function (MockInterface $mock) use ($digest): void {
            $mock->expects('__invoke')->once()->with($digest)->andReturn('digest text');
        });

        $this->mock(TwitterV2Api::class, function (MockInterface $mock) use ($now): void {
            $mock->expects('post')
                ->once()
                ->with('tweets', ['text' => 'digest text'])
                ->andReturnUsing(function () use ($now) {
                    // fix3: postが呼ばれる時点で、既にstatus=pendingのログ行が書き込まれているはず。
                    $this->assertDatabaseHas('x_digest_logs', [
                        'status' => XDigestLogStatus::Pending->value,
                        'article_count' => 4,
                        'cutoff_at' => $now->toDateTimeString(),
                    ]);

                    return ['data' => ['id' => '123']];
                });
            $mock->expects('getLastHttpCode')->once()->andReturn(201);
        });

        $this->artisan('sns:x-daily-digest')->assertSuccessful();

        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'article_count' => 4,
            'status' => XDigestLogStatus::Success->value,
            'cutoff_at' => $now->toDateTimeString(),
        ]);
    }

    public function test_does_not_resolve_twitter_v2_api_when_there_is_nothing_to_post(): void
    {
        // fix2: TwitterV2Apiはhandle()の引数ではなく使用直前でのみ解決されるべきなので、
        // 早期returnする分岐(対象0件)ではコンテナ解決(PKCEトークン参照等)が一切発生しない。
        // これを、解決されると必ず例外を投げるバインディングに差し替えて証明する。
        $this->app->bind(TwitterV2Api::class, function (): TwitterV2Api {
            throw new \RuntimeException('TwitterV2Api must not be resolved when there is nothing to post');
        });

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $this->mock(GetXDigestArticles::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')->once()->andReturn(new XDigestArticles(new Collection, 0));
        });

        $this->artisan('sns:x-daily-digest')->assertSuccessful();
    }

    public function test_updates_log_status_to_failed_and_does_not_advance_cutoff_on_non_2xx_response(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'sns_digest_published_at' => $now->subHour(),
            'sns_digest_updated_at' => null,
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

        $this->artisan('sns:x-daily-digest')->assertFailed();

        // cutoffは進まない(前回の成功行のみがlatestCutoff()に数えられる)。次回実行で同じ範囲を再試行できる。
        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'cutoff_at' => $now->toDateTimeString(),
            'status' => XDigestLogStatus::Failed->value,
        ]);
        $this->assertDatabaseHas('x_digest_logs', [
            'cutoff_at' => $previousCutoff->toDateTimeString(),
            'status' => XDigestLogStatus::Success->value,
        ]);
    }

    public function test_updates_log_status_to_failed_and_does_not_advance_cutoff_when_the_api_call_throws(): void
    {
        $previousCutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        XDigestLog::factory()->create(['cutoff_at' => $previousCutoff]);

        $now = CarbonImmutable::parse('2026-09-16 12:00:00');
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'sns_digest_published_at' => $now->subHour(),
            'sns_digest_updated_at' => null,
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

        $this->artisan('sns:x-daily-digest')->assertFailed();

        $this->assertDatabaseCount('x_digest_logs', 2);
        $this->assertDatabaseHas('x_digest_logs', [
            'cutoff_at' => $now->toDateTimeString(),
            'status' => XDigestLogStatus::Failed->value,
        ]);

        // 失敗行はlatestCutoff()に数えられないため、次回実行のcutoffは前回成功時のまま。
        $repository = app(XDigestLogRepository::class);
        $latestCutoff = $repository->latestCutoff();
        $this->assertNotNull($latestCutoff);
        $this->assertTrue($latestCutoff->equalTo($previousCutoff));
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(XDailyDigestCommand::class);

        $this->assertSame('sns:x-daily-digest', $command->getName());
    }
}
