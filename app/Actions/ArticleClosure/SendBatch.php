<?php

declare(strict_types=1);

namespace App\Actions\ArticleClosure;

use App\Enums\ArticleClosureBatchStatus;
use App\Models\ArticleClosureBatch;
use App\Models\User;
use App\Notifications\ArticleClosureDigest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class SendBatch
{
    public function __invoke(int $batchId): void
    {
        /** @var array{User, ArticleClosureDigest, string}|null $prepared */
        $prepared = DB::transaction(function () use ($batchId): ?array {
            $batch = ArticleClosureBatch::query()->lockForUpdate()->find($batchId);
            if (! $batch || ! in_array($batch->status, [ArticleClosureBatchStatus::Pending, ArticleClosureBatchStatus::Failed], true)
                || $batch->attempts >= config('article-closure.max_attempts')
                || ($batch->next_attempt_at && $batch->next_attempt_at->isFuture())
                || ! $batch->run->finalized_at) {
                return null;
            }

            try {
                $user = $batch->user;
                if (! $user || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $batch->update(['status' => ArticleClosureBatchStatus::Skipped, 'dispatch_lease_until' => null, 'last_error' => 'Recipient is unavailable.']);

                    return null;
                }
                $items = $batch->items()->orderBy('id')->get();
                if ($items->isEmpty()) {
                    $batch->update(['status' => ArticleClosureBatchStatus::Skipped, 'dispatch_lease_until' => null, 'last_error' => 'No closure items.']);

                    return null;
                }
                $html = view('emails.article-closure-digest', ['items' => $items, 'user' => $user, 'run' => $batch->run])->render();
                $notification = new ArticleClosureDigest($items->count(), $html);
                $source = config('article-closure.mailer') ?? config('mail.default');
                $mailerConfig = config('mail.mailers.'.$source);
                if (! is_array($mailerConfig)) {
                    throw new RuntimeException('Article closure mailer configuration is missing.');
                }
                if (($mailerConfig['transport'] ?? null) === 'smtp') {
                    $mailerConfig['timeout'] = config('article-closure.smtp_timeout');
                }
                $mailerName = $notification->mailerName();
                if (config('mail.mailers.'.$mailerName) !== $mailerConfig) {
                    config(['mail.mailers.'.$mailerName => $mailerConfig]);
                    Mail::purge($mailerName);
                }
                Mail::mailer($mailerName);
            } catch (Throwable $error) {
                $attempts = $batch->attempts + 1;
                $batch->update([
                    'status' => ArticleClosureBatchStatus::Failed,
                    'dispatch_lease_until' => null,
                    'attempts' => $attempts,
                    'last_error' => Str::limit($error->getMessage(), 1500),
                    'next_attempt_at' => $attempts < config('article-closure.max_attempts')
                        ? now()->addSeconds(config('article-closure.retry_delay') * $attempts) : null,
                ]);
                Log::warning('Article closure digest preparation failed', ['batch_id' => $batchId, 'attempt' => $attempts]);

                return null;
            }

            $token = (string) Str::uuid();
            $batch->update(['status' => ArticleClosureBatchStatus::Sending, 'dispatch_lease_until' => null, 'sending_token' => $token,
                'sending_at' => now(), 'attempts' => $batch->attempts + 1, 'next_attempt_at' => null, 'last_error' => null]);

            return [$user, $notification, $token];
        });

        if ($prepared === null) {
            return;
        }

        [$user, $notification, $token] = $prepared;
        try {
            $user->notifyNow($notification);
            ArticleClosureBatch::query()->whereKey($batchId)->where('status', ArticleClosureBatchStatus::Sending)
                ->where('sending_token', $token)->update(['status' => ArticleClosureBatchStatus::Sent, 'sent_at' => now()]);
        } catch (Throwable $error) {
            ArticleClosureBatch::query()->whereKey($batchId)->where('status', ArticleClosureBatchStatus::Sending)
                ->where('sending_token', $token)->update(['status' => ArticleClosureBatchStatus::Unknown,
                    'last_error' => Str::limit($error->getMessage(), 1500)]);
            Log::warning('Article closure digest delivery outcome unknown', ['batch_id' => $batchId]);
        }
    }
}
