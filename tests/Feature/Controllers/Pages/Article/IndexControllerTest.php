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
}
