<?php

namespace Tests\Feature;

use Tests\ArticleTestCase;

class MiscTest extends ArticleTestCase
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
