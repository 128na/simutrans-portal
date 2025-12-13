<?php

declare(strict_types=1);

namespace App\Services\Google\Recaptcha;

use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;
use Google\Cloud\RecaptchaEnterprise\V1\Event;

final class RecaptchaService
{
    private const float ALLOW_SCORE = 0.5;

    public function __construct(
        private RecaptchaEnterpriseServiceClient $recaptchaEnterpriseServiceClient,
        private string $projectName,
        private Event $event,
    ) {}

    public function __destruct()
    {
        $this->recaptchaEnterpriseServiceClient->close();
    }

    public function assessment(string $token): void
    {
        $this->event->setToken($token)->setExpectedAction('invite');

        $assessment = (new Assessment)->setEvent($this->event);
        $createAssessmentRequest = (new CreateAssessmentRequest)
            ->setParent($this->projectName)
            ->setAssessment($assessment);
        $response = $this->recaptchaEnterpriseServiceClient->createAssessment($createAssessmentRequest);

        if ($response->getTokenProperties()?->getValid() !== true) {
            throw new RecaptchaFailedException('token invalid');
        }

        $this->validate($response);
    }

    private function validate(Assessment $assessment): void
    {
        if ($assessment->getRiskAnalysis()?->getScore() < self::ALLOW_SCORE) {
            throw new RecaptchaHighRiskException('score too low');
        }

        if ($assessment->getTokenProperties()?->getAction() !== $assessment->getEvent()?->getExpectedAction()) {
            throw new RecaptchaHighRiskException('action mismatch');
        }
    }
}
