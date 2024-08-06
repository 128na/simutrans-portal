<?php

declare(strict_types=1);

namespace Tests\Browser;

use Database\Seeders\DuskSeeder;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Laravel\Dusk\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected $seeder = DuskSeeder::class;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(SetCacheHeaders::class);
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     */
    final public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    #[\Override]
    protected function driver()
    {
        $chromeOptions = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), fn ($items) => $items->merge([
            '--disable-gpu',
            '--headless',
        ]))->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $chromeOptions
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    #[\Override]
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
            isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    #[\Override]
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
            isset($_ENV['DUSK_START_MAXIMIZED']);
    }
}
