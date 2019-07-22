<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\PakAddonCount;
use App\Models\UserAddonCount;
use Illuminate\Http\Request;

/**
 * sitemap
 * @see https://gitlab.com/Laravelium/Sitemap
 * @see https://www.sitemaps.org/protocol.html
 */
class SitemapController extends Controller
{
    public function index()
    {
        // create new sitemap object
        $sitemap = \App::make('sitemap');

        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        // by default cache is disabled
        $sitemap->setCache('laravel.sitemap', config('app.cache_lifetime_min'), true);

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached()) {
            // listing pages
            $sitemap->add(route('index'), now(), '0.9', 'daily');
            $sitemap->add(route('addons.index'), now(), '0.8', 'weekly');
            $sitemap->add(route('pages.index'), now(), '0.3', 'monthly');
            $sitemap->add(route('announces.index'), now(), '0.3', 'monthly');

            // articles
            foreach (Article::active()->cursor() as $article) {
                $sitemap->add(
                    route('articles.show', $article->slug), $article->updated_at,
                    self::PRIORITIES[$article->post_type] ?? '0.5',
                    'monthly'
                );
            }

            // users
            foreach (UserAddonCount::cursor() as $user_addon) {
                $latest = Article::active()->where('user_id', $user_addon->user_id)->first();
                $sitemap->add(
                    route('user', $user_addon->user_id), $latest->updated_at,
                    '0.3',
                    'weekly'
                );
            }

            // pak/addons
            foreach (PakAddonCount::cursor() as $pak_addon) {
                $latest = Article::active()
                    ->whereHas('categories', function($query) use ($pak_addon) {
                        $query->pak()->slug($pak_addon->pak_slug);
                    })
                    ->whereHas('categories', function($query) use ($pak_addon) {
                        $query->addon()->slug($pak_addon->addon_slug);
                    })
                    ->first();
                $sitemap->add(
                    route('category.pak.addon', [$pak_addon->pak_slug, $pak_addon->addon_slug]), $latest->updated_at,
                    '0.3',
                    'weekly'
                );
            }
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');
    }

    const PRIORITIES = [
        'addon-post'         => '1.0',
        'addon-introduction' => '0.8',
        'page'               => '0.5',
    ];
}
