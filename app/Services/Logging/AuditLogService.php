<?php

declare(strict_types=1);

namespace App\Services\Logging;

use App\Models\Article;
use App\Models\BulkZip;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Tag;
use App\Models\User;
use App\Services\Service;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Psr\Log\LoggerInterface;

class AuditLogService extends Service
{
    private LoggerInterface $audit;

    public function __construct(LogManager $logManager)
    {
        $this->audit = $logManager->channel('audit');
    }

    public function discordInviteCodeCreate(Request $request): void
    {
        $this->audit->info('Disocrd招待リンク生成', $this->getAccessInfo($request));
    }

    public function recapchaAssessment(Assessment $assessment): void
    {
        $this->audit->info('Recaptcha', $this->getAssesmentInfo($assessment));
    }

    public function discordInviteCodeReject(Request $request): void
    {
        $this->audit->error('Disocrd招待リンク生成失敗', $this->getAccessInfo($request));
    }

    public function inviteCodeCreate(User $user): void
    {
        $this->audit->info('招待コード発行', $this->getUserInfo($user));
    }

    public function inviteCodeDelete(User $user): void
    {
        $this->audit->info('招待コード削除', $this->getUserInfo($user));
    }

    public function userInvited(User $newUser, User $user): void
    {
        $this->audit->info('新規ユーザー招待', ['newUserId' => $newUser->id, 'newUserName' => $newUser->name, 'invitedById' => $user->id, 'invitedByName' => $user->name]);
    }

    public function userLoggedIn(User $user): void
    {
        $this->audit->info('ログイン', $this->getUserInfo($user));
    }

    public function userVerified(User $user): void
    {
        $this->audit->info('メール認証', $this->getUserInfo($user));
    }

    public function tagDescriptionUpdate(User $user, Tag $tag, ?string $oldDesc, string $newDesc): void
    {
        $this->audit->info('タグ説明更新', array_merge(
            $this->getUserInfo($user),
            ['tagId' => $tag->id, 'tagName' => $tag->name, 'tagOldDesc' => $oldDesc, 'tagNewDesc' => $newDesc]
        ));
    }

    public function articleCreated(Article $article): void
    {
        $this->audit->info('記事作成', $this->getArticleInfo($article));
    }

    public function articleUpdated(Article $article): void
    {
        $this->audit->info('記事更新', $this->getArticleInfo($article));
    }

    public function deadLinkDetected(Article $article): void
    {
        /** @var AddonIntroductionContent */
        $contents = $article->contents;
        $this->audit->warning('リンク切れ検知', [
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleUrl' => route('articles.show', $article),
            'descUrl' => $contents->link,
        ]);
    }

    public function bulkZipRequest(BulkZip $bulkZip): void
    {
        $this->audit->info('BulkZip作成開始', ['id' => $bulkZip->id]);
    }

    public function bulkZipCreated(BulkZip $bulkZip, float $time): void
    {
        $this->audit->info('BulkZip作成完了', ['id' => $bulkZip->id, 'time' => sprintf('%.2f', $time)]);
    }

    public function bulkZipDelete(BulkZip $bulkZip): void
    {
        $this->audit->info('BulkZip削除', ['id' => $bulkZip->id]);
    }

    /**
     * @return array<mixed>
     */
    private function getAccessInfo(Request $request): array
    {
        return [
            'REMOTE_ADDR' => $request->server('REMOTE_ADDR', 'N/A'),
            'HTTP_REFERER' => $request->server('HTTP_REFERER', 'N/A'),
            'HTTP_USER_AGENT' => $request->server('HTTP_USER_AGENT', 'N/A'),
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getArticleInfo(Article $article): array
    {
        return [
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleStatus' => $article->status,
            'userName' => $article->user?->name,
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getUserInfo(User $user): array
    {
        return [
            'userId' => $user->id,
            'userName' => $user->name,
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getAssesmentInfo(Assessment $response): array
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
}
