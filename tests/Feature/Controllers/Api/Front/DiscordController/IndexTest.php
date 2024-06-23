<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\DiscordController;

use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaFailedException;
use App\Services\Google\Recaptcha\RecaptchaService;
use Error;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    public function test_create(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->expects()->assessment('')->once()->andReturnNull();
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->expects()->create()->once()->andReturn('dummy');
        });

        $url = '/api/front/invite-simutrans-interact-meeting';
        $testResponse = $this->postJson($url);
        $testResponse
            ->assertOk()
            ->assertJson(['url' => 'dummy']);
    }

    public function test_create_discordエラー時は400レスポンス(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->expects()->assessment('')->once()->andReturnNull();
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->expects()->create()->once()->andThrow(new Error('dummy'));
        });
        $url = '/api/front/invite-simutrans-interact-meeting';
        $testResponse = $this->postJson($url);
        $testResponse
            ->assertStatus(400)
            ->assertJson(['url' => null]);
    }

    public function test_create_recaptchaエラー時は400レスポンス(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->expects()->assessment('')->once()->andThrow(new RecaptchaFailedException('dummy'));
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->expects()->create()->never();
        });
        $url = '/api/front/invite-simutrans-interact-meeting';
        $testResponse = $this->postJson($url);
        $testResponse
            ->assertStatus(400)
            ->assertJson(['url' => null]);
    }
}
