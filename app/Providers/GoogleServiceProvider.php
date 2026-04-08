<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google\Recaptcha\RecaptchaService;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<class-string>
     */
    #[\Override]
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
    #[\Override]
    public function register(): void
    {
        $this->app->bind(function (): \App\Services\Google\Recaptcha\RecaptchaService {
            $credentials = json_decode(
                @file_get_contents(base_path(Config::string('services.google_recaptcha.credential'))) ?: '{}',
                true
            );
            $recaptchaEnterpriseServiceClient = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $recaptchaEnterpriseServiceClient->projectName(Config::string('services.google_recaptcha.projectName'));

            return new RecaptchaService(
                $recaptchaEnterpriseServiceClient,
                $projectName,
                resolve(Event::class),
            );
        });

        $this->app->bind(function (): \Google\Cloud\RecaptchaEnterprise\V1\Event {
            $event = new Event;
            $event->setSiteKey(Config::string('services.google_recaptcha.siteKey'));

            return $event;
        });
    }
}
