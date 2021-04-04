<?php

namespace Tests\Feature\Http\Controllers\Front\ArticleController;

use App\Models\Tag;
use Closure;
use Tests\ArticleTestCase;

class ListingTest extends ArticleTestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tag = Tag::factory()->create();
    }

    /**
     * @dataProvider dataListing
     * */
    public function testListing(string $name)
    {
        $response = $this->get(route($name));
        $response->assertOk();
    }

    public function dataListing()
    {
        $this->refreshApplication();

        yield 'トップ' => ['index'];
        yield 'アドオン一覧' => ['addons.index'];
        yield 'ランキング一覧' => ['addons.ranking'];
        yield '記事一覧' => ['pages.index'];
        yield 'お知らせ一覧' => ['announces.index'];
    }

    /**
     * ユーザーの投稿一覧が表示されること.
     */
    public function testUsers()
    {
        $response = $this->get('/user/'.$this->user->id);
        $response->assertOk();

        $response = $this->get('/user/wrong-id');
        $response->assertNotFound();
    }

    /**
     * @dataProvider dataCategories
     */
    public function testCategories(string $type, string $slug, bool $ok)
    {
        $response = $this->get(route('category', [$type, $slug]));
        if ($ok) {
            $response->assertOk();
        } else {
            $response->assertNotFound();
        }
    }

    public function dataCategories()
    {
        $this->refreshApplication();

        $types = array_filter(config('category.type'), fn ($type) => $type !== 'post');
        foreach ($types as $type) {
            foreach (config('category.'.$type) as $category) {
                yield "{$type}/{$category['slug']}" => [$type, $category['slug'], true];
            }
            yield "{$type}/invalid-slug" => [$type, 'invalid-slug', false];
        }

        foreach (config('category.'.$type) as $category) {
            yield "invalid-type/{$category['slug']}" => ['invalid-type', $category['slug'], false];
        }

        yield 'invalid-type/invalid-slug' => ['invalid-type', 'invalid-slug', false];
    }

    /**
     * @dataProvider dataPakAddonCategories
     */
    public function testPakAddonCategories(string $pak, string $addon, bool $ok)
    {
        $response = $this->get(route('category.pak.addon', [$pak, $addon]));
        if ($ok) {
            $response->assertOk();
        } else {
            $response->assertNotFound();
        }
    }

    public function dataPakAddonCategories()
    {
        $this->refreshApplication();

        foreach (config('category.pak') as $pak) {
            foreach (config('category.addon') as $addon) {
                yield "{$pak['slug']}/{$addon['slug']}" => [$pak['slug'], $addon['slug'], true];
            }
            yield "{$pak['slug']}/invalid-addon" => [$pak['slug'], 'invalid-addon', false];
        }

        foreach (config('category.addon') as $addon) {
            yield "invalid-pak/{$addon['slug']}" => ['invalid-pak', $addon['slug'], false];
        }
        yield 'invalid-pak/invalid-addon' => ['invalid-pak', 'invalid-addon', false];
    }

    public function test_タグ一覧()
    {
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
    }

    /**
     * @dataProvider dataTags
     */
    public function test_記事の無いタグ(Closure $fn, bool $should_see)
    {
        // assertSeeではキャッシュされたgzipがテキストにパースされないのでログインしておく
        $this->actingAs($this->user);

        Closure::bind($fn, $this)();

        // 記事に紐づいていないタグ 表示されないこと
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
        if ($should_see) {
            $res->assertSee($this->tag->name);
        } else {
            $res->assertDontSee($this->tag->name);
        }
    }

    public function dataTags()
    {
        yield '記事に紐づいていないタグ' => [fn () => null, false];
        yield '公開されている記事に紐づいてるタグ' => [fn () => $this->article->tags()->sync([$this->tag->id]), true];
        yield '非公開の記事に紐づいてるタグ' => [function () {
            $this->article->update(['status' => 'draft']);
            $this->article->tags()->sync([$this->tag->id]);
        }, false];
    }
}
