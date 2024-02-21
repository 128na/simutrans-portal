<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google\Recaptcha\RecaptchaService;
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
     */
    public function register(): void
    {
        $this->app->bind(RecaptchaService::class, static function () : \App\Services\Google\Recaptcha\RecaptchaService {
            $credentials = json_decode(
                @file_get_contents(base_path(config('services.google_recaptcha.credential'))) ?: '{}',
                true
            );
            $recaptchaEnterpriseServiceClient = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $recaptchaEnterpriseServiceClient->projectName(config('services.google_recaptcha.projectName'));
            return new RecaptchaService(
                $recaptchaEnterpriseServiceClient,
                $projectName,
                app(Event::class),
            );
        });

        $this->app->bind(Event::class, static function () : \Google\Cloud\RecaptchaEnterprise\V1\Event {
            $event = new Event();
            $event->setSiteKey(config('services.google_recaptcha.siteKey'));
            return $event;
        });
    }
}
