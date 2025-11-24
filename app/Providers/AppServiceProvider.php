<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        // 型安全な環境変数アクセスをシングルトンとして登録
        $this->app->singleton(\App\Config\EnvironmentConfig::class, function () {
            return \App\Config\EnvironmentConfig::fromEnv();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict(! App::isProduction());
        $this->registerMarkdownBladeDirective();
        $this->registerSlowQueryLogging();
    }

    private function registerMarkdownBladeDirective(): void
    {
        Blade::directive(
            'markdown',
            fn (string $expression): string => sprintf(
                '<?php echo app('.\App\Services\MarkdownService::class.'::class)->toEscapedHTML(%s); ?>',
                $expression
            )
        );
    }

    private function registerSlowQueryLogging(): void
    {
        DB::listen(function ($query): void {
            if ($query->time > 1000) {
                Log::channel('slowquery')->warning('over 1sec', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);

                return;
            }

            if ($query->time > 100) {
                Log::channel('slowquery')->info('over 100ms', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);

                return;
            }
        });
    }
}
