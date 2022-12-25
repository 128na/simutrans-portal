<?php

namespace App\Services\BulkZip\Decorators;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class AddonIntroductionDecorator extends BaseDecorator
{
    public function canProcess(Model $model): bool
    {
        return $model instanceof Article
            && $model->post_type === 'addon-introduction';
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
        if ($model->has_thumbnail) {
            $result = $this->addFile(
                $result,
                $this->toPath($model->id, $model->thumbnail->original_name),
                $model->thumbnail->path
            );
        }
        $result = $this->addContent($result, $this->content($model));

        return $result;
    }

    /**
     * @return array<mixed>
     */
    private function content(Article $model): array
    {
        /**
         * @var \App\Models\Contents\AddonIntroductionContent $contents
         */
        $contents = $model->contents;

        return [
            ['ID', $model->id],
            ['タイトル', $model->title],
            ['記事URL', route('articles.show', $model->slug)],
            ['サムネイル画像', $model->has_thumbnail
                ? $this->toPath($model->id, $model->thumbnail->original_name)
                : '無し',
            ],
            ['投稿者', $model->user->name],
            ['カテゴリ', ...$model->categories->map(fn (Category $c) => __("category.{$c->type}.{$c->slug}"))->toArray()],
            ['タグ', ...$model->tags()->pluck('name')->toArray()],
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
