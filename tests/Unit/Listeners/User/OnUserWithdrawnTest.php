<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\User;

use App\Events\User\UserWithdrawn;
use App\Listeners\User\OnUserWithdrawn;
use App\Models\User;
use Illuminate\Log\Logger;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnUserWithdrawnTest extends TestCase
{
    public function test_監査ログにユーザー退会が記録される(): void
    {
        $infoLogging = ['userId' => 1, 'userName' => 'テストユーザー'];

        /** @var User&MockInterface */
        $userMock = $this->mock(User::class, function (MockInterface $mock) use ($infoLogging): void {
            $mock->expects()->getInfoLogging()->once()->andReturn($infoLogging);
        });

        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock) use ($infoLogging): void {
            $channelMock = $this->mock(Logger::class);
            $channelMock->expects()->info('ユーザー退会', $infoLogging)->once();
            $mock->expects()->channel('audit')->once()->andReturn($channelMock);
        });

        $listener = new OnUserWithdrawn($loggerMock);
        $event = new UserWithdrawn($userMock);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }
}
