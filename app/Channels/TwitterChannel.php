<?php

declare(strict_types=1);

namespace App\Channels;

use App\Actions\SendSNS\Article\ToTwitter;
use App\Models\Article;
use App\Notifications\SendSNSNotification;
use Exception;
use Illuminate\Database\Eloquent\Model;

class TwitterChannel extends BaseChannel
{
    public function __construct()
    {
    }

    public function send(Model $model, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $model instanceof Article => app(ToTwitter::class)($model, $sendSNSNotification),
            default => throw new Exception(sprintf('unsupport model "%s" provided', $model::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.twitter.client_id');
    }
}
