<?php

namespace Tests\Feature\Http\Controllers\Front;

use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('index');
    }

    public function test()
    {
        $response = $this->get($this->url);

        $response->assertOk();
    }
}
