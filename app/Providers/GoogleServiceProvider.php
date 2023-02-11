<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google\Recaptcha\RecaptchaService;
use App\Services\Logging\AuditLogService;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<class-string>
     */
    public function provides()
    {
        return [
            RecaptchaService::class,
            Event::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RecaptchaService::class, function () {
            $credentials = json_decode(
                @file_get_contents(base_path(config('services.google_recaptcha.credential'))) ?: '{}',
                true
            );
            $client = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $client->projectName(config('services.google_recaptcha.projectName'));

            return new RecaptchaService(
                $client,
                $projectName,
                app(Event::class),
                app(AuditLogService::class),
            );
        });

        $this->app->bind(Event::class, function () {
            $event = new Event();
            $event->setSiteKey(config('services.google_recaptcha.siteKey'));

            return $event;
        });
    }
}
