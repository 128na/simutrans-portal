<?php

declare(strict_types=1);

namespace App\Channels;

use App\Actions\SendSNS\Article\ToMisskey as ArticleToMisskey;
use App\Actions\SendSNS\Screenshot\ToMisskey as ScreenshotToMisskey;
use App\Models\Article;
use App\Models\Screenshot;
use App\Notifications\SendSNSNotification;
use Exception;
use Illuminate\Database\Eloquent\Model;

final class MisskeyChannel extends BaseChannel
{
    public function __construct(
    ) {}

    public function send(Model $model, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $model instanceof Article => app(ArticleToMisskey::class)($model, $sendSNSNotification),
            $model instanceof Screenshot => app(ScreenshotToMisskey::class)($model, $sendSNSNotification),
            default => throw new Exception(sprintf('unsupport model "%s" provided', $model::class)),
        };
    }

    #[\Override]
    public static function featureEnabled(): bool
    {
        return (bool) config('services.misskey.token');
    }
}
