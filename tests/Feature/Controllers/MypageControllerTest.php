<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class MypageControllerTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('mypage.index');
    }

    public function test()
    {
        $response = $this->get($this->url);

        $response->assertOk();
    }

    public function testAny()
    {
        $url = $this->url.'/foo';
        $response = $this->get($url);
        $response->assertOk();
    }
}
