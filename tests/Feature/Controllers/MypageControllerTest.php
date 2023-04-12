<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Tests\TestCase;

final class MypageControllerTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('mypage.index');
    }

    public function test(): void
    {
        $response = $this->get($this->url);

        $response->assertOk();
    }

    public function testAny(): void
    {
        $url = $this->url.'/foo';
        $response = $this->get($url);
        $response->assertOk();
    }
}
