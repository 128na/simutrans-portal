<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\MyList;
use App\Models\Tag;
use App\Models\User;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // 一覧ページ
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setPriority(1.0))
            ->add(Url::create('/latest')->setPriority(0.7))
            ->add(Url::create('/pak128-japan')->setPriority(0.7))
            ->add(Url::create('/pak128')->setPriority(0.7))
            ->add(Url::create('/pak64')->setPriority(0.7))
            ->add(Url::create('/pak-others')->setPriority(0.7))
            ->add(Url::create('/users')->setPriority(0.8))
            ->add(Url::create('/tags')->setPriority(0.8))
            ->add(Url::create('/categories')->setPriority(0.8))
            ->add(Url::create('/pages')->setPriority(0.6))
            ->add(Url::create('/mylist')->setPriority(0.6))
            ->add(Url::create('/announces')->setPriority(0.6))
            ->add(Url::create('/search')->setPriority(0.6))
            ->add(Url::create('/social')->setPriority(0.5));

        // 記事を1つ以上投稿しているユーザーすべて
        User::has('articles')->get()->each(function (User $user) use ($sitemap) {
            $sitemap->add(Url::create(route('users.show', $user->nickname ?? $user->id))->setPriority(0.7));
        });

        // 記事に紐づいているタグすべて
        Tag::has('articles')->get()->each(function (Tag $tag) use ($sitemap) {
            $sitemap->add(Url::create(route('tags.show', $tag->name))->setPriority(0.6));
        });

        // 公開マイリストすべて
        MyList::where('is_public', true)->get()->each(function (MyList $myList) use ($sitemap) {
            $sitemap->add(Url::create(route('public-mylist.show', $myList->slug))->setPriority(0.7));
        });

        // 公開記事一覧すべて
        Article::where('status', ArticleStatus::Publish)->with('user')->latest()->take(1000)->get()->each(function (Article $article) use ($sitemap) {
            $sitemap->add(Url::create(route('articles.show', [
                'userIdOrNickname' => $article->user->nickname ?? $article->user->id,
                'articleSlug' => $article->slug,
            ]))->setPriority(0.9));
        });

        return $sitemap->toResponse(request());
    }
}
