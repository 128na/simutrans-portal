<?php

declare(strict_types=1);

namespace App\Services\Google\Recaptcha;

use App\Services\Service;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;

class RecaptchaService extends Service
{
    private const ALLOW_SCORE = 0.5;

    public function __construct(
        private RecaptchaEnterpriseServiceClient $client,
        private string $projectName,
        private Event $event,
    ) {
    }

    public function __destruct()
    {
        $this->client->close();
    }

    public function assessment(string $token): void
    {
        $this->event->setToken($token)->setExpectedAction('invite');

        $assessment = (new Assessment())->setEvent($this->event);
        $response = $this->client->createAssessment($this->projectName, $assessment);

        if ($response->getTokenProperties()?->getValid() !== true) {
            throw new RecaptchaFailedException('token invalid');
        }

        $this->validate($response);
    }

    private function validate(Assessment $response): void
    {
        if ($response->getRiskAnalysis()?->getScore() < self::ALLOW_SCORE) {
            throw new RecaptchaHighRiskException('score too low');
        }
        if ($response->getTokenProperties()?->getAction() !== $response->getEvent()?->getExpectedAction()) {
            throw new RecaptchaHighRiskException('action mismatch');
        }
    }
}
