<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Redirect;
use Tests\TestCase;

class RedirectControllerTest extends TestCase
{
    public function test(): void
    {
        Redirect::create([
            'from' => '/foo',
            'to' => '/bar',
        ]);
        $response = $this->get('/foo');
        $response->assertRedirect('/bar');
    }

    public function test404(): void
    {
        $response = $this->get('/foo');
        $response->assertNotFound();
    }
}
