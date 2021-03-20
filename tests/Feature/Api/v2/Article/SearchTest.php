<?php

namespace Tests\Feature\Api\v2\Article;

use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * @dataProvider dataStatus
     */
    public function testShow(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();

        $url = route('api.v2.articles.search', ['word' => $this->article->title]);

        $res = $this->getJson($url);
        $res->assertStatus(200);

        if ($should_see) {
            $res->assertJsonFragment(['title' => $this->article->title]);
        } else {
            $res->assertJsonMissing(['title' => $this->article->title]);
        }
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, ?string $errorField)
    {
        $url = route('api.v2.articles.search', $data);
        $res = $this->getJson($url);
        if (is_null($errorField)) {
            $res->assertStatus(200);
        } else {
            $res->assertJsonValidationErrors($errorField);
        }
    }

    public function dataValidation()
    {
        yield 'wordがnull' => [
            ['word' => null], 'word', ];
        yield 'wordが空' => [
            ['word' => ''], 'word', ];
        yield 'wordが101文字以上' => [
            ['word' => str_repeat('a', 101)], 'word', ];
        yield 'wordが文字以外' => [
            ['word' => ['array']], 'word', ];
        yield 'wordが100文字以下' => [
            ['word' => str_repeat('a', 100)], null, ];
    }

    public function testSearchResult()
    {
        $url = route('api.v2.articles.search', ['word' => $this->article->title.'_hoge']);
        $res = $this->getJson($url);
        $res->assertStatus(200);
        $res->assertJsonMissing(['title' => $this->article->title]);
    }
}
