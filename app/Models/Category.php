<?php

namespace App\Models;
use App\Models\Article;
use App\Traits\Slugable;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Slugable;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
    public function parent()
    {
        return $this->belongsTo(Category::class,'parent_id')->where('parent_id', null)->with('parent');
    }
    public function children()
    {
        return $this->hasMany(Category::class,'parent_id')->with('children');
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    public function scopeParents($query)
    {
        return $query->where('parent_id', null)
            ->whereIn('slug', ['post-type', 'pak', 'type']);
    }

    /*
    |--------------------------------------------------------------------------
    | アクセサ
    |--------------------------------------------------------------------------
    */
    public function getHasParentAttribute()
    {
        return !is_null($this->parent_id);
    }
}
