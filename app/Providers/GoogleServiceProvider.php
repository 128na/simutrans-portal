<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Google\Recaptcha\RecaptchaService;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Config;
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
            $config = $this->app->make(\App\Config\EnvironmentConfig::class);
            $credentials = json_decode(
                @file_get_contents(base_path($config->googleRecaptchaCredential ?? '')) ?: '{}',
                true
            );
            $recaptchaEnterpriseServiceClient = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $recaptchaEnterpriseServiceClient->projectName($config->googleRecaptchaProjectName ?? '');

            return new RecaptchaService(
                $recaptchaEnterpriseServiceClient,
                $projectName,
                app(Event::class),
            );
        });

        $this->app->bind(function (): \Google\Cloud\RecaptchaEnterprise\V1\Event {
            $config = $this->app->make(\App\Config\EnvironmentConfig::class);
            $event = new Event;
            $event->setSiteKey($config->googleRecaptchaSiteKey ?? '');

            return $event;
        });
    }
}
