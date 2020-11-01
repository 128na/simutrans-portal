<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     *  トップが表示されること
     * */
    public function testTop()
    {
        $response = $this->get('/');
        $response->assertOk();
    }

    /**
     *  アドオン一覧が表示されること
     * */
    public function testAddons()
    {
        $response = $this->get('/addons');
        $response->assertOk();
    }

    /**
     *  記事一覧が表示されること
     * */
    public function testPages()
    {
        $response = $this->get('/pages');
        $response->assertOk();
    }

    /**
     *  お知らせ一覧が表示されること
     * */
    public function testAnnounces()
    {
        $response = $this->get('/announces');
        $response->assertOk();
    }

    /**
     * ユーザーの投稿一覧が表示されること
     */
    public function testUsers()
    {
        $users = User::factory()->count(20)->create();
        $this->assertGreaterThan(0, $users->count());

        foreach ($users as $user) {
            $response = $this->get('/user/' . $user->id);
            $response->assertOk();
        }
        $response = $this->get('/user/wrong-id');
        $response->assertNotFound();
    }

    /**
     * カテゴリの投稿一覧が表示されること
     */
    public function testCategories()
    {
        $categories = Category::all();
        $this->assertGreaterThan(0, $categories->count());

        foreach ($categories as $category) {
            $response = $this->get('/category/' . $category->type . '/' . $category->slug);
            $response->assertOk();

            $response = $this->get('/category/wrong-type/' . $category->slug);
            $response->assertNotFound();

            $response = $this->get('/category/' . $category->type . '/wrong-slug');
            $response->assertNotFound();
        }
        $response = $this->get('/category/wrong-type/wrong-slug');
        $response->assertNotFound();
    }

    /**
     * pak/addonの投稿一覧が表示されること
     */
    public function testPakAddonCategories()
    {
        $paks = Category::pak()->get();
        $this->assertGreaterThan(0, $paks->count());
        $addons = Category::addon()->get();
        $this->assertGreaterThan(0, $addons->count());

        foreach ($paks as $pak) {
            foreach ($addons as $addon) {
                $response = $this->get('/category/pak/' . $pak->slug . '/' . $addon->slug);
                $response->assertOk();

                $response = $this->get('/category/pak/wrong-pak/' . $addon->slug);
                $response->assertNotFound();

                $response = $this->get('/category/pak/' . $pak->slug . '/wrong-addon');
                $response->assertNotFound();
            }
        }
        $response = $this->get('/category/pak/wrong-pak/wrong-addon');
        $response->assertNotFound();
    }

    // public function testSetLanguage()
    // {
    //     $response = $this->get('/language/ja');
    //     $this->assertCookie($response, 'lang', 'ja');

    //     $response = $this->get('/language/en');
    //     $this->assertCookie($response, 'lang', 'en');

    //     $response = $this->get('/language/de');
    //     $this->assertCookie($response, 'lang', 'de');

    //     $response = $this->get('/language/zh-CN');
    //     $this->assertCookie($response, 'lang', 'zh-CN');

    //     $response = $this->get('/language/zh-TW');
    //     $this->assertCookie($response, 'lang', 'zh-TW');

    //     $response = $this->get('/language/invalid');
    //     $this->assertCookie($response, 'lang', null);
    // }

    /**
     * assertPlainCookieでlangが拾えないので代用
     */
    private function assertCookie($response, $name, $value)
    {
        $cookies = $response->headers->getCookies();

        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $name) {
                return $this->assertEquals($cookie->getValue(), $value);
            }
        }
        return $this->assertEquals(null, $value);
    }
}
