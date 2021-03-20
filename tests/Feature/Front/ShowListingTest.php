<?php

namespace Tests\Feature\Front;

use Tests\TestCase;

class ShowListingTest extends TestCase
{
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
}
