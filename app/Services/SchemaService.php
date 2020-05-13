<?php
namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;

class SchemaService extends Service
{
    public function __construct()
    {
    }

    public function forTop()
    {
        $schemas = collect([]);
        $schemas->push($this->schemaSearch());
        return $schemas;
    }

    public function forShow(Article $article)
    {
        $schemas = collect([]);
        $schemas->push($this->schemaArticle($article));
        $schemas->push($this->schemaArticleBreadcrumb($article));
        return $schemas;
    }

    public function forList($name, $articles = null)
    {
        $schemas = collect([]);
        $schemas->push($this->schemaListBreadcrumb($name));
        if ($articles && $articles->count()) {
            $schemas->push($this->schemaCarouselArticles($articles));
        }
        return $schemas;
    }

    public function forPakAddon(Category $pak, Category $addon, $articles)
    {
        $schemas = collect([]);
        $schemas->push($this->schemaPakAddonBreadcrumb($pak, $addon));
        if ($articles && $articles->count()) {
            $schemas->push($this->schemaCarouselArticles($articles));
        }
        return $schemas;
    }

    public function forTag(Tag $tag, $articles)
    {
        $schemas = collect([]);
        $schemas->push($this->schemaTagBreadcrumb($tag));
        if ($articles && $articles->count()) {
            $schemas->push($this->schemaCarouselArticles($articles));
        }
        return $schemas;
    }

    private function schemaSearch()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => route('index'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search') . '?word={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private function schemaArticle(Article $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'name' => $article->title,
            'author' => [
                '@type' => 'Person',
                'name' => $article->contents->author ?? $article->user->name,
            ],
            'datePublished' => $article->created_at,
            'headline' => $article->headline_description,
            'image' => [$article->thumbnail_url ? $article->thumbnail_url : asset('storage/default/ogp-image.png')],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('storage/default/logo.png'),
                    'height' => 60,
                    'width' => 240,
                    'name' => config('app.name'),
                ],
            ],
            'dateModified' => $article->updated_at,
            'description' => $article->meta_description,
            'mainEntityOfPage' => route('articles.show', $article->slug),
        ];
    }

    private function schemaArticleBreadcrumb(Article $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Top'),
                    'item' => route('index'),
                ], [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('User :name', ['name' => $article->user->name]),
                    'item' => route('user', $article->user),
                ], [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $article->title,
                    'item' => route('articles.show', $article->slug),
                ],
            ]];
    }

    private function schemaListBreadcrumb($name)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Top'),
                    'item' => route('index'),
                ], [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __($name),
                    'item' => url()->current(),
                ],
            ]];
    }

    private function schemaPakAddonBreadcrumb(Category $pak, Category $addon)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Top'),
                    'item' => route('index'),
                ], [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('category.pak.' . $pak->slug),
                    'item' => route('category', ['pak', $pak->slug]),
                ], [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => __('category.addon.' . $addon->slug),
                    'item' => url()->current(),
                ],
            ]];
    }
    private function schemaTagBreadcrumb(Tag $tag)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Top'),
                    'item' => route('index'),
                ], [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('Tags'),
                    'item' => route('tags'),
                ], [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $tag->name,
                    'item' => url()->current(),
                ],
            ]];
    }

    private function schemaCarouselArticles($articles)
    {
        $items = $articles->map(fn ($article, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $article->title,
                'url' => route('articles.show', $article->slug),
            ]);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $items,
        ];
    }
}
