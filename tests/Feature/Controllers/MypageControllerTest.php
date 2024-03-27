<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers;

use Tests\Feature\TestCase;

class MypageControllerTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('mypage.index');
    }

    public function test(): void
    {
        $testResponse = $this->get($this->url);

        $testResponse->assertOk();
    }

    public function testAny(): void
    {
        $url = $this->url.'/foo';
        $response = $this->get($url);
        $response->assertOk();
    }
}
