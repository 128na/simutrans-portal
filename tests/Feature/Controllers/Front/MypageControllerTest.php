<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Front;

use Tests\Feature\TestCase;

final class MypageControllerTest extends TestCase
{
    private string $url;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('login');
    }

    public function test(): void
    {
        $testResponse = $this->get($this->url);

        $testResponse->assertOk();
    }

    public function test_any(): void
    {
        $url = $this->url . '/foo';
        $testResponse = $this->get($url);
        $testResponse->assertNotFound();
    }
}
