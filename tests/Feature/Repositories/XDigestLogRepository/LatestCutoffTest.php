<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\XDigestLogRepository;

use App\Enums\XDigestLogStatus;
use App\Models\XDigestLog;
use App\Repositories\XDigestLogRepository;
use Carbon\CarbonImmutable;
use Tests\Feature\TestCase;

class LatestCutoffTest extends TestCase
{
    private XDigestLogRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(XDigestLogRepository::class);
    }

    public function test_returns_null_when_no_logs_exist(): void
    {
        $this->assertNull($this->repository->latestCutoff());
    }

    public function test_returns_the_maximum_cutoff_at_among_multiple_success_logs(): void
    {
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-14 12:00:00')]);
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 21:30:00')]);
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-15 12:00:00')]);

        $latest = $this->repository->latestCutoff();

        $this->assertNotNull($latest);
        $this->assertTrue($latest->equalTo(CarbonImmutable::parse('2026-09-16 21:30:00')));
    }

    public function test_ignores_pending_and_failed_logs_even_when_their_cutoff_is_the_latest(): void
    {
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-14 12:00:00')]);
        XDigestLog::factory()->pending()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 21:30:00')]);
        XDigestLog::factory()->failed()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-17 12:00:00')]);

        $latest = $this->repository->latestCutoff();

        $this->assertNotNull($latest);
        $this->assertTrue($latest->equalTo(CarbonImmutable::parse('2026-09-14 12:00:00')));
    }

    public function test_returns_null_when_only_pending_or_failed_logs_exist(): void
    {
        XDigestLog::factory()->pending()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 12:00:00')]);
        XDigestLog::factory()->failed()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-17 12:00:00')]);

        $this->assertNull($this->repository->latestCutoff());
    }

    public function test_create_via_has_crud_trait_persists_a_log_row(): void
    {
        $this->repository->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 12:00:00'), 'article_count' => 3, 'status' => XDigestLogStatus::Success]);

        $this->assertDatabaseHas('x_digest_logs', ['article_count' => 3, 'status' => XDigestLogStatus::Success->value]);
    }

    public function test_update_via_has_crud_trait_changes_the_status_of_an_existing_log_row(): void
    {
        $log = $this->repository->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 12:00:00'), 'article_count' => 1, 'status' => XDigestLogStatus::Pending]);

        $this->repository->update($log, ['status' => XDigestLogStatus::Success]);

        $this->assertDatabaseHas('x_digest_logs', ['id' => $log->id, 'status' => XDigestLogStatus::Success->value]);
    }
}
