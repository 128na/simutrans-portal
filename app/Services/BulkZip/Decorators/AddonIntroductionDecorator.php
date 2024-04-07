<?php

declare(strict_types=1);

namespace App\Services\BulkZip\Decorators;

use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

final class AddonIntroductionDecorator extends BaseDecorator
{
    public function canProcess(Model $model): bool
    {
        return $model instanceof Article
            && $model->post_type === ArticlePostType::AddonIntroduction;
    }

    /**
     * Zip格納データに変換する.
     *
     * @param  Article  $model
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
     * @return array<int,array<int,string>>
     */
    private function content(Article $article): array
    {
        /**
         * @var \App\Models\Contents\AddonIntroductionContent $contents
         */
        $contents = $article->contents;

        return [
            ['ID', (string) $article->id],
            ['タイトル', $article->title],
            ['記事URL', route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug])],
            [
                'サムネイル画像', $article->has_thumbnail && $article->thumbnail
                    ? $this->toPath($article->id, $article->thumbnail->original_name)
                    : '無し',
            ],
            ['投稿者', $article->user->name],
            ['カテゴリ', ...$article->categories->map(fn (Category $category): string => __(sprintf('category.%s.%s', $category->type->value, $category->slug)))],
            ['タグ', ...$article->tags->map(fn (Tag $tag): string => $tag->name)],
            ['作者 / 投稿者', $contents->author ?? ''],
            ['説明', $contents->description ?? ''],
            ['謝辞・参考にしたアドオン', $contents->thanks ?? ''],
            ['ライセンス', $contents->license ?? ''],
            ['掲載許可', $contents->agreement ? 'Yes' : 'No'],
            ['掲載先URL', $contents->link ?? ''],
            ['リンク切れチェック', $contents->exclude_link_check ? 'No' : 'Yes'],
            ['------'],
        ];
    }
}
