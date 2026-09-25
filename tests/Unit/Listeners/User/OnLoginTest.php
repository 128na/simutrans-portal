<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\User;

use App\Listeners\User\OnLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_does_not_process_when_user_is_not_user_instance(): void
    {
        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock): void {
            $mock->expects()->channel(\Mockery::any())->never();
        });

        $listener = new OnLogin($loggerMock);
        $event = new Login('web', new \stdClass, false);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }
}
