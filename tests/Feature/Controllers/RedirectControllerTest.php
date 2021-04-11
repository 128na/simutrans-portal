<?php

namespace Tests\Feature\Controllers;

use App\Models\Redirect;
use Tests\TestCase;

class RedirectControllerTest extends TestCase
{
    public function test()
    {
        Redirect::create([
            'from' => '/foo',
            'to' => '/bar',
        ]);
        $response = $this->get('/foo');
        $response->assertRedirect('/bar');
    }

    public function test404()
    {
        $response = $this->get('/foo');
        $response->assertNotFound();
    }
}
