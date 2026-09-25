<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\Article;

use App\Enums\ArticlePostType;
use App\Http\Requests\Article\UpdateRequest;
use App\Models\Article;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

/**
 * 投稿種別ごとのルール（BaseRequest::addonPost() 等）の網羅的な検証は
 * StoreRequestTest::dataFail() で行う（BaseRequest::rules() は final で
 * Store/Update 共通のため、片方で網羅すれば十分）。
 *
 * 一方 baseRule() は Store/Update で別々に書かれており共通化されていないため
 * （status/title/slug/published_at/articles など）、baseRule 由来のケース
 * （status/title/slug の必須・書式）は投稿種別1つ（AddonIntroduction）分を
 * ここでも網羅する。加えて UpdateRequest 固有の差分（post_type 必須の有無、
 * title unique の自記事除外、without_update_modified_at / follow_redirect）と、
 * 投稿種別ルールが Update でも効いていることの代表ケースを検証する。
 */
class UpdateRequestTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[DataProvider('dataBaseRuleFail')]
    public function test_base_rule_fail(Closure $setup, string $expectedErrorField): void
    {
        $article = $this->createAddonIntroduction($this->user);

        $data = ['article' => [
            'id' => $article->id,
            'post_type' => ArticlePostType::AddonIntroduction->value,
            ...$setup($this),
        ]];

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    public static function dataBaseRuleFail(): \Generator
    {
        yield 'ステータスが空' => [
            fn (self $self): array => ['status' => ''],
            'article.status',
        ];
        yield '不正なステータス' => [
            fn (self $self): array => ['status' => 'test_example'],
            'article.status',
        ];
        yield 'タイトルが空' => [
            fn (self $self): array => ['title' => ''],
            'article.title',
        ];
        yield 'タイトルが256文字以上' => [
            fn (self $self): array => ['title' => str_repeat('a', 256)],
            'article.title',
        ];
        yield 'タイトルにNG文字' => [
            fn (self $self): array => ['title' => '@example'],
            'article.title',
        ];
        yield 'スラッグが空' => [
            fn (self $self): array => ['slug' => ''],
            'article.slug',
        ];
        yield 'スラッグが256文字以上' => [
            fn (self $self): array => ['slug' => str_repeat('a', 256)],
            'article.slug',
        ];
    }

    public function test_addon_type_rule_is_applied_on_update(): void
    {
        $article = $this->createAddonPost($this->user);

        $data = ['article' => [
            'id' => $article->id,
            'post_type' => ArticlePostType::AddonPost->value,
            'contents' => ['file' => ''],
        ]];

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey('article.contents.file', $messageBag->toArray());
    }

    public function test_title_is_not_unique_violation_when_same_as_own_article(): void
    {
        $article = $this->createAddonIntroduction($this->user);

        $data = $this->validAddonIntroductionData($article, ['title' => $article->title]);

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayNotHasKey('article.title', $messageBag->toArray());
    }

    public function test_title_is_unique_violation_when_same_as_other_article(): void
    {
        $article = $this->createAddonIntroduction($this->user);
        $other = Article::factory()->create();

        $data = $this->validAddonIntroductionData($article, ['title' => $other->title]);

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey('article.title', $messageBag->toArray());
    }

    public function test_post_type_is_not_required_on_update(): void
    {
        $article = $this->createAddonIntroduction($this->user);

        $data = $this->validAddonIntroductionData($article);
        unset($data['article']['post_type']);

        $this->actingAs($this->user);
        $validator = $this->makeValidator(UpdateRequest::class, $data);
        $this->assertTrue($validator->passes(), $validator->errors()->toJson());
    }

    #[DataProvider('dataInvalidBooleanFlags')]
    public function test_update_only_flags_reject_non_boolean_values(string $field): void
    {
        $article = $this->createAddonIntroduction($this->user);

        $data = $this->validAddonIntroductionData($article, [], [$field => 'not_boolean']);

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($field, $messageBag->toArray());
    }

    public static function dataInvalidBooleanFlags(): \Generator
    {
        yield 'without_update_modified_at が真偽値でない' => ['without_update_modified_at'];
        yield 'follow_redirect が真偽値でない' => ['follow_redirect'];
    }

    #[DataProvider('dataValidBooleanFlags')]
    public function test_update_only_flags_accept_boolean_values(string $field): void
    {
        $article = $this->createAddonIntroduction($this->user);

        $data = $this->validAddonIntroductionData($article, [], [$field => true]);

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayNotHasKey($field, $messageBag->toArray());
    }

    public static function dataValidBooleanFlags(): \Generator
    {
        yield 'without_update_modified_at' => ['without_update_modified_at'];
        yield 'follow_redirect' => ['follow_redirect'];
    }

    /**
     * @param  array<string, mixed>  $articleOverrides
     * @param  array<string, mixed>  $topLevelOverrides
     * @return array<string, mixed>
     */
    private function validAddonIntroductionData(Article $article, array $articleOverrides = [], array $topLevelOverrides = []): array
    {
        return array_merge([
            'article' => array_merge([
                'id' => $article->id,
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'status' => $article->status->value,
                'title' => $article->title,
                'slug' => $article->slug,
                'contents' => [
                    'author' => 'author',
                    'link' => 'https://example.com',
                    'description' => 'description',
                ],
                'categories' => [],
                'tags' => [],
                'articles' => [],
            ], $articleOverrides),
        ], $topLevelOverrides);
    }
}
