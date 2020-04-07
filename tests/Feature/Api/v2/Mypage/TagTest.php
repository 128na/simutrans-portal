<?php

namespace Tests\Feature\Api\v2\Mypage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testIndex()
    {
    }
    public function testStore()
    {
    }
}
