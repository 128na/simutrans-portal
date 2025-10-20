<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\MarkdownService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);
        Model::shouldBeStrict(! App::isProduction());
        Blade::directive('markdown', function ($expression) {
            return "<?php echo app(App\Services\MarkdownService::class)->toEscapedHTML($expression); ?>";
        });
    }
}
