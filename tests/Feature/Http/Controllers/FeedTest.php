<?php

namespace Tests\Feature\Http\Controllers;

use Tests\ArticleTestCase;

class FeedTest extends ArticleTestCase
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
