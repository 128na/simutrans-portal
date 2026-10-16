<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\LangJsonExportCommand;
use Illuminate\Console\Command;
use Mockery;
use Tests\Feature\TestCase;

class LangJsonExportCommandTest extends TestCase
{
    public function test_missing_locale_reports_warning_without_exporting(): void
    {
        $this->artisan('lang:export-json', ['--locales' => 'nonexistent-test-locale'])
            ->expectsOutput("⚠️  Locale 'nonexistent-test-locale' not found in resources/lang/")
            ->assertSuccessful();
    }

    public function test_non_string_locales_option_fails(): void
    {
        $command = Mockery::mock(LangJsonExportCommand::class)->makePartial();
        $command->shouldReceive('option')->with('locales')->once()->andReturn([]);
        $command->shouldReceive('error')->with('The locales option must be a comma-separated string.')->once();

        $this->assertSame(Command::FAILURE, $command->handle());
    }
}
