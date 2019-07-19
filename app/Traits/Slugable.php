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
        return $this->slug(urlencode($value))->first() ?? $this->findOrFail($value);
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
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
        $value = urlencode($value);
        $this->attributes['slug'] = $value;
    }

    /**
     * スラッグがユニークか
     */
    public function isUniqueSlug()
    {
        $query = self::where('slug', $this->slug);
        // IDがある＝保存済みなら自身を除く
        if ($this->id) {
            $query->where('id', '<>', $this->id);
        }
        return $query->doesntExist();
    }
}
