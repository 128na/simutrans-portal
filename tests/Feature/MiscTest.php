<?php

namespace Tests\Feature;

use Tests\TestCase;

class MiscTest extends TestCase
{
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
