<?php

declare(strict_types=1);

namespace App\Channels;

use App\Actions\SendSNS\Article\ToOneSignal as ArticleToOneSignal;
use App\Models\Article;
use App\Notifications\SendSNSNotification;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class OneSignalChannel extends BaseChannel
{
    public function __construct() {}

    public function send(Model $model, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $model instanceof Article => app(ArticleToOneSignal::class)($model, $sendSNSNotification),
            default => throw new Exception(sprintf('unsupport model "%s" provided', $model::class)),
        };
    }

    #[\Override]
    public static function featureEnabled(): bool
    {
        return config('onesignal.app_id') && config('onesignal.rest_api_key');
    }
}
