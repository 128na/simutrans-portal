<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Slug.
 *
 * @template T of \Illuminate\Database\Eloquent\Model
 */
trait Slugable
{
    /**
     * スラッグがユニークか.
     */
    public function isUniqueSlug(): bool
    {
        $query = self::where('slug', $this->slug);
        // IDがある＝保存済みなら自身を除く
        if ($this->id) {
            $query->where('id', '<>', $this->id);
        }

        return $query->doesntExist();
    }

    /**
     * 生のスラッグ入力値を、DBに保存される形式（正規化 + urlencode()済み）に変換する。
     *
     * {@see setSlugAttribute()}が保存時に使うのと全く同じ変換を公開しており、
     * 保存前に「最終的にどのスラッグ値になるか」を知る必要がある呼び出し元
     * （例: 重複チェックのバリデーション）はこのメソッドを使うこと。
     */
    public static function normalizeSlug(string $value): string
    {
        $value = urldecode($value);
        $value = mb_strtolower($value);

        $replaces = ['!', '"', '#', '$', '%', '&', "'", '(', ')', '*', '+', ',', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '`', '{', '|', '}', ' ', '　', '.'];
        $value = str_replace($replaces, '-', $value);

        return urlencode($value);
    }

    /**
     * @param  Builder<T>  $builder
     * @return Builder<T>
     */
    protected function scopeSlug(Builder $builder, string $slug): Builder
    {
        return $builder->where('slug', urlencode($slug));
    }

    protected function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = self::normalizeSlug($value);
    }
}
