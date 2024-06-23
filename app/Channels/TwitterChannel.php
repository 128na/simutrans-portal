<?php

declare(strict_types=1);

namespace App\Channels;

use App\Actions\SendSNS\Article\ToTwitter as ArticleToTwitter;
use App\Actions\SendSNS\Screenshot\ToTwitter as ScreenshotToTwitter;
use App\Models\Article;
use App\Models\Screenshot;
use App\Notifications\SendSNSNotification;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class TwitterChannel extends BaseChannel
{
    public function __construct() {}

    public function send(Model $model, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $model instanceof Article => app(ArticleToTwitter::class)($model, $sendSNSNotification),
            $model instanceof Screenshot => app(ScreenshotToTwitter::class)($model, $sendSNSNotification),
            default => throw new Exception(sprintf('unsupport model "%s" provided', $model::class)),
        };
    }

    #[\Override]
    public static function featureEnabled(): bool
    {
        return (bool) config('services.twitter.client_id');
    }
}
