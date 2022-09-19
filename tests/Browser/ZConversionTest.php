<?php

namespace Tests\Browser;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ZConversionTest extends DuskTestCase
{
    private Article $article1;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();
        Article::query()->delete();
        $user = User::factory()->create();
        $category = Category::where('type', 'pak')->where('slug', '128')->first();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create([
            'user_id' => $user->id,
        ]);
        $this->article1->categories()->save($category);

        $this->article2 = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->article2->categories()->save($category);
    }

    public function test()
    {
        $this->browse(function (Browser $browser) {
            $dayly = now()->format('Ymd');
            $monthly = now()->format('Ym');
            $yearly = now()->format('Y');
            $total = 'total';

            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total]);

            $browser
                ->visit('/')
                ->waitForText($this->article1->title)
                ->clickLink($this->article1->title)
                ->assertPathIs("/articles/{$this->article1->slug}")
                ->click('@conversion-link')
            ;
            sleep(1);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total, 'count' => 1]);

            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article2->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article2->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article2->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article2->id, 'type' => '4', 'period' => $total]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article2->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article2->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article2->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article2->id, 'type' => '4', 'period' => $total]);

            $browser
                ->visit('/')
                ->waitForText($this->article2->title)
                ->clickLink($this->article2->title)
                ->assertPathIs("/articles/{$this->article2->slug}")
                ->click('@conversion-download')
            ;
            sleep(1);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article2->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article2->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article2->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article2->id, 'type' => '4', 'period' => $total, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article2->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article2->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article2->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article2->id, 'type' => '4', 'period' => $total, 'count' => 1]);
        });
    }
}
