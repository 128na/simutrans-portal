<?php

namespace Tests\Feature\Controllers\Front;

use Tests\TestCase;

class AdvancedSearchControllerTest extends TestCase
{
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('advancedSearch');
    }

    public function test未ログイン()
    {
        $response = $this->get($this->url);
        $response->assertRedirect(route('verification.notice'));
    }

    public function testメールアドレス未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $response = $this->get($this->url);
        $response->assertRedirect(route('verification.notice'));
    }

    public function testメールアドレス認証済み()
    {
        $this->actingAs($this->user);
        $response = $this->get($this->url);
        $response->assertOk();
    }

    public function testPOST()
    {
        $this->actingAs($this->user);
        $response = $this->post($this->url);
        $response->assertOk();
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, ?string $exceptedError)
    {
        $this->actingAs($this->user);
        $response = $this->post($this->url, ['advancedSearch' => $data]);
        if ($exceptedError) {
            $response->assertSessionHasErrors($exceptedError);
        } else {
            $response->assertOk();
        }
    }

    public function dataValidation()
    {
        yield 'word 100文字以下' => [
            ['word' => str_repeat('a', 100)],
            null,
        ];
        yield 'word 101文字以上' => [
            ['word' => str_repeat('a', 101)],
            'advancedSearch.word',
        ];

        yield 'categoryIds 存在しないカテゴリ' => [
            ['categoryIds' => [-1]],
            'advancedSearch.categoryIds.0',
        ];
        yield 'categoryAnd bool以外' => [
            ['categoryAnd' => []],
            'advancedSearch.categoryAnd',
        ];
        yield 'categoryAnd bool' => [
            ['categoryAnd' => 1],
            null,
        ];

        yield 'tagIds 存在しないカテゴリ' => [
            ['tagIds' => [-1]],
            'advancedSearch.tagIds.0',
        ];
        yield 'tagAnd bool以外' => [
            ['tagAnd' => []],
            'advancedSearch.tagAnd',
        ];
        yield 'tagAnd bool' => [
            ['tagAnd' => 1],
            null,
        ];

        yield 'userIds 存在しないカテゴリ' => [
            ['userIds' => [-1]],
            'advancedSearch.userIds.0',
        ];
        yield 'userAnd bool以外' => [
            ['userAnd' => []],
            'advancedSearch.userAnd',
        ];
        yield 'userAnd bool' => [
            ['userAnd' => 1],
            null,
        ];

        yield 'startAt 日付' => [
            ['startAt' => now()->toDateString()],
            null,
        ];
        yield 'startAt 日付以外' => [
            ['startAt' => 1],
            'advancedSearch.startAt',
        ];

        yield 'endAt 日付' => [
            ['endAt' => now()->toDateString()],
            null,
        ];
        yield 'endAt 日付以外' => [
            ['endAt' => 1],
            'advancedSearch.endAt',
        ];
        yield 'endAt startAtより過去' => [
            ['startAt' => now()->toDateString(), 'endAt' => now()->yesterday()->toDateString()],
            'advancedSearch.endAt',
        ];

        yield 'order 指定値' => [
            ['order' => 'created_at'],
            null,
        ];
        yield 'order 指定値以外' => [
            ['order' => 'foo'],
            'advancedSearch.order',
        ];

        yield 'direction 指定値' => [
            ['direction' => 'desc'],
            null,
        ];
        yield 'direction 指定値以外' => [
            ['direction' => 'foo'],
            'advancedSearch.direction',
        ];
    }
}
