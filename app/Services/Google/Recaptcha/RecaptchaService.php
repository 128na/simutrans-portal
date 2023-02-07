<?php

declare(strict_types=1);

namespace App\Services\Google\Recaptcha;

use App\Services\Service;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

class RecaptchaService extends Service
{
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
        $this->event->setToken($token);

        $assessment = (new Assessment())->setEvent($this->event);
        $response = $this->client->createAssessment($this->projectName, $assessment);
        $info = $this->getInfo($response);
        logger()->info('assessment result', $info);

        if ($response->getTokenProperties()?->getValid() !== true) {
            throw new RecaptchaFailedException();
        }

        $this->validate($response);
    }

    /**
     * @return array<mixed>
     */
    private function getInfo(Assessment $response): array
    {
        // https://cloud.google.com/recaptcha-enterprise/docs/interpret-assessment?hl=ja
        return [
            // risk
            'score' => $response->getRiskAnalysis()?->getScore(),
            'reasons' => $response->getRiskAnalysis()?->getReasons(),
            // token
            'action' => $response->getTokenProperties()?->getAction(),
            'timestamp' => $response->getTokenProperties()?->getCreateTime()?->getSeconds(),
            'hostname' => $response->getTokenProperties()?->getHostname(),
            // event
            'hashedAccountId' => $response->getEvent()?->getHashedAccountId(),
            'userAgent' => $response->getEvent()?->getUserAgent(),
            'userIpAddress' => $response->getEvent()?->getUserIpAddress(),
            'invalidReason' => InvalidReason::name($response->getTokenProperties()?->getInvalidReason()),
        ];
    }

    private function validate(Assessment $response): void
    {
        if ($response->getRiskAnalysis()?->getScore() < 0.8) {
            throw new RecaptchaHighRiskException('score too low');
        }
        if ($response->getTokenProperties()?->getAction() !== $response->getEvent()?->getExpectedAction()) {
            throw new RecaptchaHighRiskException('action mismatch');
        }
    }
}
