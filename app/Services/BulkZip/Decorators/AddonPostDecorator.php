<?php

declare(strict_types=1);

namespace App\Services\BulkZip\Decorators;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class AddonPostDecorator extends BaseDecorator
{
    public function canProcess(Model $model): bool
    {
        return $model instanceof Article
            && $model->post_type === 'addon-post';
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

        // アドオンファイル
        if ($model->file) {
            $result = $this->addFile(
                $result,
                $this->toPath($model->id, $model->file->original_name),
                $model->file->path
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
         * @var \App\Models\Contents\AddonPostContent $contents
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
            ['カテゴリ', ...$article->categories->map(static fn (Category $category) => __(sprintf('category.%s.%s', $category->type, $category->slug)))->toArray()],
            ['タグ', ...$article->tags()->pluck('name')->toArray()],
            ['作者 / 投稿者', $contents->author],
            ['説明', $contents->description],
            ['謝辞・参考にしたアドオン', $contents->thanks],
            ['ライセンス', $contents->license],
            ['アドオンファイル', $this->toPath($article->id, $article->file->original_name ?? '')],
            ['------'],
        ];
    }
}
