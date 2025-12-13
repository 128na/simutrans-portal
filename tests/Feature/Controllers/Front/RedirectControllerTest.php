<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Front;

use App\Models\Redirect;
use Tests\Feature\TestCase;

class RedirectControllerTest extends TestCase
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

    public function test_redirect(): void
    {
        $testResponse = $this->get(route('redirect', ['name' => 'test']));
        $testResponse->assertRedirectToRoute('index');

        $testResponse = $this->get(route('redirect', ['name' => 'simutrans-interact-meeting']));
        $testResponse->assertRedirectToRoute('articles.fallbackShow', ['id' => 1212]);
    }
}
