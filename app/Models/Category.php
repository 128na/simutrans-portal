<?php

namespace App\Models;
use App\Models\Article;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
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
        return $this->belongsTo(Category::class,'parent_id')->where('parent_id',0)->with('parent');
    }
    public function children()
    {
        return $this->hasMany(Category::class,'parent_id')->with('children');
    }
}
