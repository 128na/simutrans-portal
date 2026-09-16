<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\XDigestLogRepository;

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

    public function test_returns_the_maximum_cutoff_at_among_multiple_logs(): void
    {
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-14 12:00:00')]);
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 21:30:00')]);
        XDigestLog::factory()->create(['cutoff_at' => CarbonImmutable::parse('2026-09-15 12:00:00')]);

        $latest = $this->repository->latestCutoff();

        $this->assertNotNull($latest);
        $this->assertTrue($latest->equalTo(CarbonImmutable::parse('2026-09-16 21:30:00')));
    }

    public function test_create_via_has_crud_trait_persists_a_log_row(): void
    {
        $this->repository->create(['cutoff_at' => CarbonImmutable::parse('2026-09-16 12:00:00'), 'article_count' => 3]);

        $this->assertDatabaseHas('x_digest_logs', ['article_count' => 3]);
    }
}
