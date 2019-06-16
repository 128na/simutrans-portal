<?php
namespace App\Traits;

/**
 * Slug
 */
trait Slugable
{
    /*
    |--------------------------------------------------------------------------
    | モデル取得
    |--------------------------------------------------------------------------
    */
    public function resolveRouteBinding($value)
    {
        return is_numeric($value)
            ? $this->findOrFail($value)
            : ($this->where('slug', $value)->first() ?? $this->findOrFail($value));
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function setSlugAttribute($value)
    {
        $value = strtolower($value);
        $replaces = ['!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '`', '{', '|', '}', ' ', '　', '.'];
        $value = str_replace($replaces, '-', $value);
        $this->attributes['slug'] = $value;
    }
}
