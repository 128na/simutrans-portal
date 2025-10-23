<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;
use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaFailedException;
use App\Services\Google\Recaptcha\RecaptchaService;
use Mockery\MockInterface;

final class DiscordControllerTest extends TestCase
{
    public function test_index(): void
    {
        $testResponse = $this->get(route('discord.index'));
        $testResponse->assertOk();
    }
    public function test_generate(): void
    {
        $this->mock(RecaptchaService::class, function (MockInterface $mock): void {
            $mock->expects()->assessment('dummy')->once()->andReturnNull();
        });
        $this->mock(InviteService::class, function (MockInterface $mock): void {
            $mock->expects()->create()->once()->andReturn('https://example.com/dummy');
        });

        $testResponse = $this->post(route('discord.index'), ['recaptchaToken' => 'dummy']);
        $testResponse->assertOk();
        $testResponse->assertSee('https://example.com/dummy');
    }
}
