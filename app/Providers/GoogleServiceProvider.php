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
            $credentialPath = base_path(Config::string('services.google_recaptcha.credential'));
            $credentials = [];

            if (is_file($credentialPath) && is_readable($credentialPath)) {
                $contents = file_get_contents($credentialPath);
                if ($contents !== false) {
                    $decoded = json_decode($contents, true);
                    if (is_array($decoded)) {
                        $credentials = $decoded;
                    }
                }
            }

            $recaptchaEnterpriseServiceClient = new RecaptchaEnterpriseServiceClient(['credentials' => $credentials]);
            $projectName = $recaptchaEnterpriseServiceClient->projectName(Config::string(
                'services.google_recaptcha.projectName',
            ));

            return new RecaptchaService($recaptchaEnterpriseServiceClient, $projectName, app(Event::class));
        });

        $this->app->bind(function (): \Google\Cloud\RecaptchaEnterprise\V1\Event {
            $event = new Event();
            $event->setSiteKey(Config::string('services.google_recaptcha.siteKey'));

            return $event;
        });
    }
}
