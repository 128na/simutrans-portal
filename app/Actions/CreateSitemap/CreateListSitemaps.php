<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Generator;
use Illuminate\Support\LazyCollection;
use Spatie\Sitemap\Tags\Url;

final class CreateListSitemaps
{
    public function __invoke(SitemapHandler $sitemapHandler): void
    {
        foreach ($this->getUrlSet() as $filename => $urls) {
            foreach ($urls as $url) {
                $urlTag = Url::create($url)
                    ->setChangeFrequency('daily')
                    ->setPriority(0.3);
                $sitemapHandler->add($filename, $urlTag);
            }
        }
    }

    /**
     * @return Generator<string,LazyCollection<int,string>>
     */
    private function getUrlSet(): Generator
    {
        yield 'user.xml' => User::has('articles')->cursor()
            ->map(fn (User $user) => route('user', ['userIdOrNickname' => $user->nickname ?? $user->id]));
        yield 'category.xml' => Category::has('articles')->cursor()
            ->map(fn (Category $category) => route('category', ['type' => $category->type->value, 'slug' => $category->slug]));
        yield 'tag.xml' => Tag::has('articles')->cursor()
            ->map(fn (Tag $tag) => route('tag', $tag));
        yield 'pak.xml' => $this->getCategoryPakAddon();
        yield 'misc.xml' => [
            '/',
            '/ranking',
            '/tags',
            '/social',
            '/invite-simutrans-interact-meeting',
            '/mypage',
            route('screenshots.index'),
        ];
    }

    private function getCategoryPakAddon()
    {
        return LazyCollection::make(function () {
            $paks = Category::pak()->get();
            $addons = Category::addon()->get();
            foreach ($paks as $pak) {
                foreach ($addons as $addon) {
                    yield route('category.pak.addon', ['size' => $pak->slug, 'slug' => $addon->slug]);
                }
            }
        });
    }
}
