<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AnalyticsController;

use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/mypage/analytics';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時に分析ページを表示(): void
    {
        Article::factory()
            ->for($this->user)
            ->count(3)
            ->publish()
            ->create();

        $url = '/mypage/analytics';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_記事がない時も正常に表示(): void
    {
        $url = '/mypage/analytics';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
