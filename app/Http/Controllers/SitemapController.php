<?php

namespace App\Http\Controllers;

use App\Models\Sitemap;

/**
 * sitemap
 * @see https://gitlab.com/Laravelium/Sitemap
 * @see https://www.sitemaps.org/protocol.html
 */
class SitemapController extends Controller
{
    private $sitemap;

    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    public function index()
    {
        $sitemap = $this->sitemap->getOrGenerate();

        return response($sitemap)->header('Content-Type', 'text/xml');
    }
}
