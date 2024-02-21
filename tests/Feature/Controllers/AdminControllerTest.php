<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

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
        $response = $this->get($this->url);

        $response->assertRedirect(route('mypage.index'));
    }

    public function testUser(): void
    {
        $this->actingAs($this->user);
        $response = $this->get($this->url);
        $response->assertUnauthorized();
    }

    public function testAdmin(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get($this->url);

        $response->assertOk();
    }
}
