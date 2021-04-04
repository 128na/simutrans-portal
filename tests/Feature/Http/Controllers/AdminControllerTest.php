<?php

namespace Tests\Feature\Http\Controllers;

use Tests\AdminTestCase;

class AdminControllerTest extends AdminTestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('admin.index');
    }

    public function testGuest()
    {
        $response = $this->get($this->url);

        $response->assertRedirect(route('mypage.index'));
    }

    public function testUser()
    {
        $this->actingAs($this->user);
        $response = $this->get($this->url);
        $response->assertUnauthorized();
    }

    public function testAdmin()
    {
        $this->actingAs($this->admin);
        $response = $this->get($this->url);

        $response->assertOk();
    }
}
