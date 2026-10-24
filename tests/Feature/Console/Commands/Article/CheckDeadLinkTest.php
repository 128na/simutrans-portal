<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Article;

use App\Actions\ArticleClosure\Recover;
use App\Actions\DeadLink\Check;
use App\Console\Commands\Article\CheckDeadLink;
use App\Enums\ArticleClosureRunStatus;
use App\Models\ArticleClosureRun;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class CheckDeadLinkTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->mock(Recover::class)->shouldReceive('__invoke')->once();
        $this->mock(Check::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::type('callable'), \Mockery::type('callable'), \Mockery::type('callable'));
        });

        $this->artisan('check:deadlink')->assertSuccessful();
        $this->assertSame(ArticleClosureRunStatus::Completed, ArticleClosureRun::sole()->status);
    }

    public function test_command_fails_when_exception_thrown(): void
    {
        $this->mock(Recover::class)->shouldReceive('__invoke')->once();
        $this->mock(Check::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->andThrow(new \Exception('Test exception'));
        });

        $this->artisan('check:deadlink')->assertFailed();
        $this->assertSame(ArticleClosureRunStatus::Interrupted, ArticleClosureRun::sole()->status);
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(CheckDeadLink::class);

        $this->assertEquals('check:deadlink', $command->getName());
    }

    public function test_command_description_exists(): void
    {
        $command = $this->app->make(CheckDeadLink::class);

        $this->assertNotEmpty($command->getDescription());
    }
}
