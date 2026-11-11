<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use DOMDocument;
use Tests\Feature\TestCase;

class IndexControllerTest extends TestCase
{
    public function test_announces(): void
    {
        $testResponse = $this->get(route('announces'));

        $testResponse->assertOk();
    }

    public function test_pages(): void
    {
        $testResponse = $this->get(route('pages'));

        $testResponse->assertOk();
    }

    public function test_search(): void
    {
        $testResponse = $this->get(route('search', ['word' => 'foo']));

        $testResponse->assertOk();
    }

    public function test_search_without_effective_conditions_explains_how_to_start(): void
    {
        foreach (['/search', '/search?word=+++', '/search?word=%E3%80%80', '/search?page=2&utm_source=test', '/search?tagIds%5B%5D='] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('キーワードを入力するか、詳細条件を指定して検索してください。')
                ->assertDontSee('条件に一致する記事が見つかりませんでした。');
        }
    }

    public function test_search_with_no_results_explains_how_to_relax_or_clear_conditions(): void
    {
        $this->createAddonIntroduction();

        $this->get(route('search', ['word' => 'unmatched-search-token']))
            ->assertOk()
            ->assertSee('条件に一致する記事が見つかりませんでした。')
            ->assertSee('検索条件をすべて解除')
            ->assertDontSee('キーワードを入力するか、詳細条件を指定して検索してください。');

        $this->get(route('search', ['word' => '0']))
            ->assertOk()->assertSee('条件に一致する記事が見つかりませんでした。');

        foreach (['categoryIds', 'tagIds', 'userIds', 'postTypes'] as $filter) {
            $this->get(route('search', [$filter => ['999999']]))
                ->assertOk()
                ->assertSee('条件に一致する記事が見つかりませんでした。');
        }
    }

    public function test_direct_search_link_with_results_keeps_the_filter_and_result(): void
    {
        $article = $this->createAddonIntroduction();
        $url = route('search', ['userIds' => [$article->user_id]]);

        $this->get($url)->assertOk()
            ->assertSee('id="app-article-cards"', false)
            ->assertSee($article->title)
            ->assertSee('検索条件をすべて解除')
            ->assertDontSee('条件に一致する記事が見つかりませんでした。');

        $this->get($url.'&page=2')->assertOk()
            ->assertSee('検索結果の最初のページへ')
            ->assertDontSee('条件に一致する記事が見つかりませんでした。');
    }

    public function test_search_preserves_selected_ids_in_html_attributes(): void
    {
        $response = $this->get('/search?word=&categoryIds%5B%5D=2&categoryIds%5B%5D=10&userIds%5B%5D=27&tagIds%5B%5D=5&tagIds%5B%5D=8');

        $response->assertOk();

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML($response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        foreach ([
            ['app-search-users', 'data-user-ids', ['27']],
            ['app-search-tags', 'data-tag-ids', ['5', '8']],
        ] as [$elementId, $attribute, $expectedIds]) {
            $element = $document->getElementById($elementId);
            $this->assertNotNull($element);
            $this->assertSame($expectedIds, json_decode($element->getAttribute($attribute), true, 512, JSON_THROW_ON_ERROR));
        }
    }

    public function test_search_selected_ids_cannot_inject_html_attributes_or_elements(): void
    {
        $ids = ['" autofocus onfocus="alert(1)" data-injected="yes', '</script><script id="injected-search-script">alert(1)</script>&\''];
        $response = $this->get('/search?'.http_build_query(['userIds' => $ids, 'tagIds' => $ids]));

        $response->assertOk();

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML($response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        foreach (['users' => 'data-user-ids', 'tags' => 'data-tag-ids'] as $kind => $attribute) {
            $element = $document->getElementById('app-search-'.$kind);
            $this->assertNotNull($element);
            $this->assertSame($ids, json_decode($element->getAttribute($attribute), true, 512, JSON_THROW_ON_ERROR));
            $this->assertFalse($element->hasAttribute('autofocus'));
            $this->assertFalse($element->hasAttribute('onfocus'));
            $this->assertFalse($element->hasAttribute('data-injected'));
        }

        $this->assertNull($document->getElementById('injected-search-script'));
    }
}
