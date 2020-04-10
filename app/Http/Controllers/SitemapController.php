<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;

class SitemapController extends Controller
{
    private $sitemap_service;

    public function __construct(SitemapService $sitemap_service)
    {
        $this->sitemap_service = $sitemap_service;
    }

    public function index()
    {
        $sitemap = $this->sitemap_service->getOrGenerate();

        return response($sitemap)->header('Content-Type', 'text/xml');
    }
}
