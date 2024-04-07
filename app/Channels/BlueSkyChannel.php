<?php

declare(strict_types=1);

namespace App\Channels;

use App\Actions\SendSNS\Article\ToBluesky as ArticleToBluesky;
use App\Actions\SendSNS\Screenshot\ToBluesky as ScreenshotToBluesky;
use App\Models\Article;
use App\Models\Screenshot;
use App\Notifications\SendSNSNotification;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class BlueSkyChannel extends BaseChannel
{
    public function __construct(
    ) {
    }

    public function send(Model $model, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $model instanceof Article => app(ArticleToBluesky::class)($model, $sendSNSNotification),
            $model instanceof Screenshot => app(ScreenshotToBluesky::class)($model, $sendSNSNotification),
            default => throw new Exception(sprintf('unsupport model "%s" provided', $model::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.bluesky.user');
    }
}
