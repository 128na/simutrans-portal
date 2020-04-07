<?php

namespace Tests\Feature\Api\v2\Mypage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachmentTest extends TestCase
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
    public function testDelete()
    {
    }
}
