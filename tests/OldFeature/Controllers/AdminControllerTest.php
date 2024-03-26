<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers;

use Tests\AdminTestCase;

class AdminControllerTest extends AdminTestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('admin.index');
    }

    public function testGuest(): void
    {
        $testResponse = $this->get($this->url);

        $testResponse->assertRedirect(route('mypage.index'));
    }

    public function testUser(): void
    {
        $this->actingAs($this->user);
        $testResponse = $this->get($this->url);
        $testResponse->assertUnauthorized();
    }

    public function testAdmin(): void
    {
        $this->actingAs($this->admin);
        $testResponse = $this->get($this->url);

        $testResponse->assertOk();
    }
}
