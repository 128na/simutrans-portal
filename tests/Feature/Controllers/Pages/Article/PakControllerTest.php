<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use Tests\Feature\TestCase;

class PakControllerTest extends TestCase
{
    public function test_pak128_japan(): void
    {
        $testResponse = $this->get(route('pak.128japan'));

        $testResponse->assertOk();
    }

    public function test_pak128(): void
    {
        $testResponse = $this->get(route('pak.128'));

        $testResponse->assertOk();
    }

    public function test_pak64(): void
    {
        $testResponse = $this->get(route('pak.64'));

        $testResponse->assertOk();
    }

    public function test_pak_others(): void
    {
        $testResponse = $this->get(route('pak.others'));

        $testResponse->assertOk();
    }
}
