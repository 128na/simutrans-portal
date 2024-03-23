<?php

declare(strict_types=1);

namespace App\Services\BulkZip\Decorators;

use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class AddonIntroductionDecorator extends BaseDecorator
{
    public function canProcess(Model $model): bool
    {
        return $model instanceof Article
            && $model->post_type === ArticlePostType::AddonIntroduction;
    }

    /**
     * Zip格納データに変換する.
     *
     * @param  array<array<mixed>>  $result
     * @param  Article  $model
     * @return array<array<mixed>>
     */
    public function process(array $result, Model $model): array
    {
        // サムネ
        if ($model->has_thumbnail && $model->thumbnail) {
            $result = $this->addFile(
                $result,
                $this->toPath($model->id, $model->thumbnail->original_name),
                $model->thumbnail->path
            );
        }

        return $this->addContent($result, $this->content($model));
    }

    /**
     * @return array<mixed>
     */
    private function content(Article $article): array
    {
        /**
         * @var \App\Models\Contents\AddonIntroductionContent $contents
         */
        $contents = $article->contents;

        return [
            ['ID', $article->id],
            ['タイトル', $article->title],
            ['記事URL', route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug])],
            [
                'サムネイル画像', $article->has_thumbnail && $article->thumbnail
                    ? $this->toPath($article->id, $article->thumbnail->original_name)
                    : '無し',
            ],
            ['投稿者', $article->user->name ?? ''],
            ['カテゴリ', ...$article->categories->map(fn (Category $category) => __(sprintf('category.%s.%s', $category->type, $category->slug)))->toArray()],
            ['タグ', ...$article->tags()->pluck('name')->toArray()],
            ['作者 / 投稿者', $contents->author],
            ['説明', $contents->description],
            ['謝辞・参考にしたアドオン', $contents->thanks],
            ['ライセンス', $contents->license],
            ['掲載許可', $contents->agreement ? 'Yes' : 'No'],
            ['掲載先URL', $contents->link],
            ['リンク切れチェック', $contents->exclude_link_check ? 'No' : 'Yes'],
            ['------'],
        ];
    }
}
