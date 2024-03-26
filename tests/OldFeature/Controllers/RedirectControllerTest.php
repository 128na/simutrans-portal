<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers;

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
        $testResponse = $this->get('/foo');
        $testResponse->assertRedirect('/bar');
    }

    public function test404(): void
    {
        $testResponse = $this->get('/foo');
        $testResponse->assertNotFound();
    }
}
