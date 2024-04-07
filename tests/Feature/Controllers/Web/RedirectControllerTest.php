<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Models\Redirect;
use Tests\Feature\TestCase;

final class RedirectControllerTest extends TestCase
{
    public function test(): void
    {
        $redirect = Redirect::factory()->create();
        $testResponse = $this->get($redirect->from);
        $testResponse->assertRedirect($redirect->to);
    }

    public function test404(): void
    {
        $testResponse = $this->get('/foo');
        $testResponse->assertNotFound();
    }
}
