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
     * サイトマップが表示されること
     */
    public function testSitemap()
    {
        $response = $this->get('/sitemap');
        $response->assertOk();

        static::createAddonPost();
        static::createAddonIntroduction();
        static::createPage();
        static::createAnnounce();

        $response = $this->get('/sitemap');
        $response->assertOk();
    }

    /**
     * feedが表示されること
     */
    public function testFeed()
    {
        $response = $this->get('/feed');
        $response->assertOk();

        static::createAddonPost();
        static::createAddonIntroduction();
        static::createPage();
        static::createAnnounce();

        $response = $this->get('/feed');
        $response->assertOk();
    }
}
