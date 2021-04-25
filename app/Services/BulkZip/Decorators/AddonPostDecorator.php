<?php

namespace App\Services\BulkZip\Decorators;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class AddonPostDecorator extends BaseDecorator
{
    public function canProcess(Model $model): bool
    {
        return get_class($model) === Article::class
            && $model->post_type === 'addon-post';
    }

    /**
     * Zip格納データに変換する.
     */
    public function process(array $result, Model $model): array
    {
        // サムネ
        if ($model->has_thumbnail) {
            $result = $this->addFile(
                $result,
                $this->toPath($model->id, $model->thumbnail->original_name),
                $model->thumbnail->path);
        }
        // アドオンファイル
        $result = $this->addFile(
            $result,
            $this->toPath($model->id, $model->file->original_name),
            $model->file->path);
        $result = $this->addContent($result, $this->content($model));

        return $result;
    }

    private function content(Article $model): array
    {
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
            ['作者 / 投稿者', $model->contents->author],
            ['説明', $model->contents->description],
            ['謝辞・参考にしたアドオン', $model->contents->thanks],
            ['ライセンス', $model->contents->license],
            ['アドオンファイル', $this->toPath($model->id, $model->file->original_name)],
            ['------'],
        ];
    }
}
