<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Article;

use App\Actions\DeadLink\Check;
use App\Actions\DeadLink\OnDead;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class CheckDeadLinkTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->mock(Check::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->with(\Mockery::type(OnDead::class));
        });

        $exitCode = $this->artisan('check:deadlink');

        $exitCode->assertSuccessful();
    }

    public function test_command_fails_when_exception_thrown(): void
    {
        $this->mock(Check::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->andThrow(new \Exception('Test exception'));
        });

        $exitCode = $this->artisan('check:deadlink');

        $exitCode->assertFailed();
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(\App\Console\Commands\Article\CheckDeadLink::class);

        $this->assertEquals('check:deadlink', $command->getName());
    }

    public function test_command_description_exists(): void
    {
        $command = $this->app->make(\App\Console\Commands\Article\CheckDeadLink::class);

        $this->assertNotEmpty($command->getDescription());
    }
}
