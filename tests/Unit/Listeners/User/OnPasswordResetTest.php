<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\User;

use App\Listeners\User\OnPasswordReset;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Log\Logger;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnPasswordResetTest extends TestCase
{
    public function test_logs_password_reset(): void
    {
        $infoLogging = ['user_id' => 1, 'email' => 'test@example.com'];

        $userMock = $this->mock(User::class, function (MockInterface $mock) use ($infoLogging): void {
            $mock->expects()->getInfoLogging()->once()->andReturn($infoLogging);
        });

        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock) use ($infoLogging): void {
            $channelMock = $this->mock(Logger::class);
            $channelMock->expects()->info('パスワードリセット', $infoLogging)->once();
            $mock->expects()->channel('audit')->once()->andReturn($channelMock);
        });

        $listener = new OnPasswordReset($loggerMock);
        $event = new PasswordReset($userMock);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }

    public function test_does_not_process_when_user_is_not_user_instance(): void
    {
        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock): void {
            $mock->expects()->channel(\Mockery::any())->never();
        });

        $listener = new OnPasswordReset($loggerMock);
        $event = new PasswordReset(new \stdClass);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }
}
