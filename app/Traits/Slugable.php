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
     * @param  Builder<T>  $builder
     * @return Builder<T>
     */
    public function scopeSlug(Builder $builder, string $slug): Builder
    {
        return $builder->where('slug', urlencode($slug));
    }

    public function setSlugAttribute(string $value): void
    {
        $value = urldecode($value);
        $value = mb_strtolower($value);

        $replaces = ['!', '"', '#', '$', '%', '&', "'", '(', ')', '*', '+', ',', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '`', '{', '|', '}', ' ', '　', '.'];
        $value = str_replace($replaces, '-', $value);
        $value = urlencode($value);
        $this->attributes['slug'] = $value;
    }

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
}
