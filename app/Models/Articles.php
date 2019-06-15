<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    protected $attributes = [
        'contents' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'title',
        'contents',
        'status',
    ];
    protected $casts = [
        'contents' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function attachments()
    {
        return $this->morphMany(Attachments::class, 'attachmentable');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
