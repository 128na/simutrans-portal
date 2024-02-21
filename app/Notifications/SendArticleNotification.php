<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\BlueSkyChannel;
use App\Channels\MisskeyChannel;
use App\Channels\OneSignalChannel;
use App\Channels\TwitterChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class SendArticleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string>
     */
    public function via(mixed $notifiable)
    {
        $enabledFilter =
            /**
             * @var class-string<\App\Channels\BaseChannel> $className
             */
            static fn (string $className) => $className::featureEnabled();

        return array_filter([
            MisskeyChannel::class,
            TwitterChannel::class,
            OneSignalChannel::class,
            BlueSkyChannel::class,
        ], $enabledFilter);
    }
}
