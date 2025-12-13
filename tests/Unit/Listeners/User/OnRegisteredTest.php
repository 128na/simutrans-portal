<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\User;

use App\Listeners\User\OnRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Log\Logger;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnRegisteredTest extends TestCase
{
    public function test_logs_user_registration(): void
    {
        $infoLogging = ['user_id' => 1, 'email' => 'test@example.com', 'name' => 'Test User'];

        $userMock = $this->mock(User::class, function (MockInterface $mock) use ($infoLogging): void {
            $mock->expects()->getInfoLogging()->once()->andReturn($infoLogging);
        });

        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock) use ($infoLogging): void {
            $channelMock = $this->mock(Logger::class);
            $channelMock->expects()->info('ユーザー登録', $infoLogging)->once();
            $mock->expects()->channel('audit')->once()->andReturn($channelMock);
        });

        $listener = new OnRegistered($loggerMock);
        $event = new Registered($userMock);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }

    public function test_does_not_process_when_user_is_not_user_instance(): void
    {
        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock): void {
            $mock->expects()->channel(\Mockery::any())->never();
        });

        $listener = new OnRegistered($loggerMock);
        $event = new Registered(new \stdClass);

        $result = $listener->handle($event);

        $this->assertNull($result);
    }

    public function test_uses_audit_log_channel(): void
    {
        $userMock = $this->mock(User::class, function (MockInterface $mock): void {
            $mock->allows()->getInfoLogging()->andReturn([]);
        });

        /** @var Logger */
        $loggerMock = $this->mock(Logger::class, function (MockInterface $mock): void {
            $channelMock = $this->mock(Logger::class);
            $channelMock->allows()->info(\Mockery::any(), \Mockery::any());
            $mock->expects()->channel('audit')->once()->andReturn($channelMock);
        });

        $listener = new OnRegistered($loggerMock);
        $event = new Registered($userMock);

        $listener->handle($event);

        // Mock expectations検証
        $this->expectNotToPerformAssertions();
    }
}
