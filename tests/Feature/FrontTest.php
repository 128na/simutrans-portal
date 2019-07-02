<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
class FrontTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     *  トップ
     * */
    public function testTop()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     *  アドオン一覧
     * */
    public function testAddons()
    {
        $response = $this->get('/addons');
        $response->assertStatus(200);
    }

    /**
     *  記事一覧
     * */
    public function testPages()
    {
        $response = $this->get('/pages');
        $response->assertStatus(200);
    }

    /**
     *  お知らせ一覧
     * */
    public function testAnnounces()
    {
        $response = $this->get('/announces');
        $response->assertStatus(200);
    }

    /**
     *  アドオン投稿
     * */
    public function testShowAddonPost()
    {
        $article = self::createAddonPost();
        $response = $this->get('/articles/'.$article->slug);
        $response->assertStatus(200);
    }

    /**
     *  アドオン紹介
     * */
    public function testShowAddonIntroduction()
    {
        $article = self::createAddonIntroduction();
        $response = $this->get('/articles/'.$article->slug);
        $response->assertStatus(200);
    }

    /**
     *  一般記事
     * */
    public function testShowPage()
    {
        $article = self::createPage();
        $response = $this->get('/articles/'.$article->slug);
        $response->assertStatus(200);

    }

    /**
     *  お知らせ
     * */
    public function testShowAnnounce()
    {
        $article = self::createAnnounce();
        $response = $this->get('/articles/'.$article->slug);
        $response->assertStatus(200);
    }


    /**
     *  存在しない記事
     * */
    public function testMissingArticle()
    {
        $article = self::createAddonIntroduction();
        $response = $this->get('/articles/'.$article->slug.'missing');
        $response->assertStatus(404);
    }

    /**
     * ユーザーの投稿一覧
     */
    public function testUsers()
    {
        $users = factory(User::class, 20)->create();
        $this->assertGreaterThan(0, $users->count());

        foreach ($users as $user) {
            $response = $this->get('/user/'.$user->id);
            $response->assertStatus(200);
        }
    }

    /**
     * カテゴリの投稿一覧
     */
    public function testCategories()
    {
        $categories = Category::all();
        $this->assertGreaterThan(0, $categories->count());

        foreach ($categories as $category) {
            $response = $this->get('/category/'.$category->type.'/'.$category->slug);
            $response->assertStatus(200);
        }
    }


    /**
     * pak/addonの投稿一覧
     */
    public function testPakAddonCategories()
    {
        $paks = Category::pak()->get();
        $this->assertGreaterThan(0, $paks->count());
        $addons = Category::addon()->get();
        $this->assertGreaterThan(0, $addons->count());

        foreach ($paks as $pak) {
            foreach ($addons as $addon) {
                $response = $this->get('/category/pak/'.$pak->slug.'/'.$addon->slug);
                $response->assertStatus(200);
            }
        }
    }




    private static function createAddonPost()
    {
        $user = factory(User::class)->create();
        $file = UploadedFile::fake()->create('document.zip', 1);
        $attachment = Attachment::createFromFile($file, $user->id);
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'addon-post',
            'title' => 'test_addon-post',
            'status' => 'publish',
            'contents' => [
                'description' => 'test addon-post text',
                'author' => 'test author',
                'file' => $attachment->id,
            ],
        ]);
        return $article;
    }
    private static function createAddonIntroduction()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'title' => 'test_addon-introduction',
            'status' => 'publish',
            'contents' => [
                'description' => 'test addon-introduction text',
                'author' => 'test author',
                'link' => 'http://example.com',
            ],
        ]);
        return $article;
    }
    private static function createPage()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_page',
            'status' => 'publish',
            'contents' => ['type' => 'text', 'text' => 'test page text'],
        ]);
        return $article;
    }
    private static function createAnnounce()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_announce',
            'status' => 'publish',
            'contents' => ['type' => 'text', 'text' => 'test announce text'],
        ]);
        $announce_category = Category::where('type', 'page')->where('slug', 'announce')->firstOrFail();
        $article->categories()->save($announce_category);
        return $article;
    }
}
