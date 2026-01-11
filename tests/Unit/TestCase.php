<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Illuminate\Foundation\Testing\TestCase からDB依存を取り除いたもの
 */
abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithConsole,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithContainer,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithSession,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithTestCaseLifecycle,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithTime,
        \Illuminate\Foundation\Testing\Concerns\InteractsWithViews,
        \Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

    /**
     * The list of trait that this test uses, fetched recursively.
     *
     * @var array<class-string, int>
     */
    protected array $traitsUsedByTest;

    /**
     * Clean up the testing environment before the next test case.
     */
    final public static function tearDownAfterClass(): void
    {
        static::tearDownAfterClassUsingTestCase();
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->setUpTheTestEnvironment();
        \Illuminate\Support\Sleep::fake();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     *
     * @throws \Mockery\Exception\InvalidCountException
     */
    protected function tearDown(): void
    {
        Mockery::close();
        $this->tearDownTheTestEnvironment();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    final public function createApplication()
    {
        $app = require Application::inferBasePath().'/bootstrap/app.php';

        $this->traitsUsedByTest = array_flip(class_uses_recursive(static::class));

        // if (
        //     isset(\Illuminate\Foundation\Testing\CachedState::$cachedConfig) &&
        //     isset($this->traitsUsedByTest[\Illuminate\Foundation\Testing\WithCachedConfig::class])
        // ) {
        //     $this->markConfigCached($app);
        // }

        // if (
        //     isset(\Illuminate\Foundation\Testing\CachedState::$cachedRoutes) &&
        //     isset($this->traitsUsedByTest[\Illuminate\Foundation\Testing\WithCachedRoutes::class])
        // ) {
        //     $app->booting(fn() => $this->markRoutesCached($app));
        // }

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $this->app = $this->createApplication();
    }

    /**
     * @param  class-string<\Illuminate\Foundation\Http\FormRequest>  $requestClass
     * @param  array<mixed>  $data
     */
    protected function makeValidator(string $requestClass, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $request = new $requestClass($data);

        return Validator::make($data, $request->rules());
    }
}
