<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

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
