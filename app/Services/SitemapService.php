<?php

namespace App\Services;

use App\Models\Article;
use App\Models\PakAddonCount;
use App\Models\UserAddonCount;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Sitemap as SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

/**
 * sitemap
 * @see https://gitlab.com/Laravelium/Sitemap
 * @see https://www.sitemaps.org/protocol.html
 */
class SitemapService
{
    private const PRIORITIES = [
        'addon-post' => '1.0',
        'addon-introduction' => '0.8',
        'page' => '0.5',
    ];
    private const FILENAME = 'sitemap.xml';

    public function getOrGenerate()
    {
        return Storage::disk('public')->exists(self::FILENAME) ? self::get() : self::generate();
    }

    private static function get()
    {
        return Storage::disk('public')->get(self::FILENAME);
    }

    public static function generate()
    {
        $sitemap = SitemapGenerator::create();

        $add = function ($url, $priority, $change_frequency, $last_modification = null) use ($sitemap) {
            $last_modification = $last_modification ?? now();

            $sitemap->add(
                Url::create($url)
                    ->setPriority($priority)
                    ->setChangeFrequency($change_frequency)
                    ->setLastModificationDate($last_modification->toDate())
            );
        };

        // listing pages
        $add(route('index'), '0.9', Url::CHANGE_FREQUENCY_DAILY);
        $add(route('addons.index'), '0.8', Url::CHANGE_FREQUENCY_WEEKLY);
        $add(route('pages.index'), '0.3', Url::CHANGE_FREQUENCY_MONTHLY);
        $add(route('announces.index'), '0.3', Url::CHANGE_FREQUENCY_MONTHLY);

        // articles
        foreach (Article::active()->cursor() as $article) {
            $add(
                route('articles.show', $article->slug),
                self::PRIORITIES[$article->post_type] ?? '0.5',
                Url::CHANGE_FREQUENCY_MONTHLY,
                $article->updated_at
            );
        }

        // users
        foreach (UserAddonCount::cursor() as $user_addon) {
            $latest = Article::active()->where('user_id', $user_addon->user_id)->first();

            if ($latest) {
                $add(
                    route('user', $user_addon->user_id),
                    '0.3',
                    Url::CHANGE_FREQUENCY_WEEKLY,
                    $latest->updated_at
                );
            }
        }

        // pak/addons
        foreach (PakAddonCount::cursor() as $pak_addon) {
            $latest = Article::active()
                ->whereHas('categories', function ($query) use ($pak_addon) {
                    $query->pak()->slug($pak_addon->pak_slug);
                })
                ->whereHas('categories', function ($query) use ($pak_addon) {
                    $query->addon()->slug($pak_addon->addon_slug);
                })
                ->first();

            if ($latest) {
                $add(
                    route('category.pak.addon', [$pak_addon->pak_slug, $pak_addon->addon_slug]),
                    '0.3',
                    Url::CHANGE_FREQUENCY_WEEKLY,
                    $latest->updated_at
                );
            }

        }
        $path = storage_path('app/public/' . self::FILENAME);
        $sitemap->writeToFile($path);

        return self::get();
    }

}
