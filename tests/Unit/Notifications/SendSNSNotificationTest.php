<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Channels\BlueSkyChannel;
use App\Channels\MisskeyChannel;
use App\Channels\OneSignalChannel;
use App\Notifications\SendSNSNotification;
use Tests\Unit\TestCase;

class SendSNSNotificationTest extends TestCase
{
    public function test_enabled_channels_are_filtered_by_config(): void
    {
        config()->set('services.misskey.token', 'token');
        config()->set('onesignal.app_id', 'app');
        config()->set('onesignal.rest_api_key', 'key');
        config()->set('services.bluesky.user', 'user');

        $notification = new class extends SendSNSNotification {};

        $channels = $notification->via(new \stdClass);

        $this->assertSame(
            [MisskeyChannel::class, OneSignalChannel::class, BlueSkyChannel::class],
            array_values($channels)
        );
    }

    public function test_no_channels_when_all_disabled(): void
    {
        config()->set('services.misskey.token', null);
        config()->set('onesignal.app_id', null);
        config()->set('onesignal.rest_api_key', null);
        config()->set('services.bluesky.user', null);

        $notification = new class extends SendSNSNotification {};

        $this->assertSame([], array_values($notification->via(new \stdClass)));
    }

    public function test_twitter_channel_is_no_longer_included_even_when_configured(): void
    {
        // X (Twitter) の即時投稿は日次集約投稿(sns:x-daily-digest)へ移行したため、
        // TwitterChannelがconfig有効時でもvia()に含まれないことを保証する。
        config()->set('services.misskey.token', 'token');
        config()->set('services.twitter.client_id', 'client-id');
        config()->set('onesignal.app_id', 'app');
        config()->set('onesignal.rest_api_key', 'key');
        config()->set('services.bluesky.user', 'user');

        $notification = new class extends SendSNSNotification {};

        $channels = array_values($notification->via(new \stdClass));

        $this->assertSame([MisskeyChannel::class, OneSignalChannel::class, BlueSkyChannel::class], $channels);
        $this->assertCount(3, $channels);
    }
}
