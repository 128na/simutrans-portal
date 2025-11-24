<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google\Recaptcha\RecaptchaService;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

final class GoogleServiceProvider extends ServiceProvider implements DeferrableProvider
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
            $environmentConfig = $this->app->make(\App\Config\EnvironmentConfig::class);
            $credentials = json_decode(
                @file_get_contents(base_path($environmentConfig->googleRecaptchaCredential ?? '')) ?: '{}',
                true
            );
            $recaptchaEnterpriseServiceClient = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $recaptchaEnterpriseServiceClient->projectName($environmentConfig->googleRecaptchaProjectName ?? '');

            return new RecaptchaService(
                $recaptchaEnterpriseServiceClient,
                $projectName,
                app(Event::class),
            );
        });

        $this->app->bind(function (): \Google\Cloud\RecaptchaEnterprise\V1\Event {
            $environmentConfig = $this->app->make(\App\Config\EnvironmentConfig::class);
            $event = new Event;
            $event->setSiteKey($environmentConfig->googleRecaptchaSiteKey ?? '');

            return $event;
        });
    }
}
