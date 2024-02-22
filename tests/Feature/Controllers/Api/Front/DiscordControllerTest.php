<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front;

use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaException;
use App\Services\Google\Recaptcha\RecaptchaService;
use Error;
use Mockery\MockInterface;
use Tests\TestCase;

class DiscordControllerTest extends TestCase
{
    public function test_create(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('assessment')->once()->andReturnNull();
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->once()->andReturn('dummy');
        });

        $url = '/api/front/invite-simutrans-interact-meeting';
        $res = $this->postJson($url);
        $res
            ->assertOk()
            ->assertJson(['url' => 'dummy']);
    }

    public function test_create_discordエラー時は400レスポンス(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('assessment')->once()->andReturnNull();
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->once()->andThrow(new Error('dummy'));
        });
        $url = '/api/front/invite-simutrans-interact-meeting';
        $res = $this->postJson($url);
        $res
            ->assertStatus(400)
            ->assertJson(['url' => null]);
    }

    public function test_create_recaptchaエラー時は400レスポンス(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('assessment')->once()->andThrow(new RecaptchaException('dummy'));
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('create')->never();
        });
        $url = '/api/front/invite-simutrans-interact-meeting';
        $res = $this->postJson($url);
        $res
            ->assertStatus(400)
            ->assertJson(['url' => null]);
    }
}
