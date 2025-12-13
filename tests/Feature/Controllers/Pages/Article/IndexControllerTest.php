<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use Tests\Feature\TestCase;

class IndexControllerTest extends TestCase
{
    public function test_announces(): void
    {
        $testResponse = $this->get(route('announces'));

        $testResponse->assertOk();
    }

    public function test_pages(): void
    {
        $testResponse = $this->get(route('pages'));

        $testResponse->assertOk();
    }

    public function test_search(): void
    {
        $testResponse = $this->get(route('search', ['word' => 'foo']));

        $testResponse->assertOk();
    }
}
