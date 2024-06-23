<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use Tests\Feature\TestCase;

final class MypageControllerTest extends TestCase
{
    private string $url;

    #[\Override]
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
        $testResponse = $this->get($url);
        $testResponse->assertOk();
    }
}
