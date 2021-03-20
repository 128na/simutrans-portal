<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiscTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     * feedが表示されること.
     */
    public function testFeed()
    {
        $response = $this->get('/feed');
        $response->assertOk();

        $this->createAddonPost();
        $this->createAddonIntroduction();
        $this->createPage();
        $this->createAnnounce();

        $response = $this->get('/feed');
        $response->assertOk();
    }
}
