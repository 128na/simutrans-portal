<?php

namespace App\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Slug.
 */
trait Slugable
{
    /**
     * @param string $value
     * @param string $field
     * @return Model
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->slug(urlencode($value))->first() ?? $this->findOrFail($value);
    }

    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function setSlugAttribute(string $value):void
    {
        $value = urldecode($value);
        $value = strtolower($value);
        $replaces = ['!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '`', '{', '|', '}', ' ', '　', '.'];
        $value = str_replace($replaces, '-', $value);
        $value = urlencode($value);
        $this->attributes['slug'] = $value;
    }

    /**
     * スラッグがユニークか.
     */
    public function isUniqueSlug():bool
    {
        $query = self::where('slug', $this->slug);
        // IDがある＝保存済みなら自身を除く
        if ($this->id) {
            $query->where('id', '<>', $this->id);
        }

        return $query->doesntExist();
    }
}
