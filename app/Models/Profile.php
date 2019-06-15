<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $attributes = [
        'data' => '{}',
    ];
    protected $fillable = [
        'user_id',
        'data',
    ];
    protected $casts = [
        'data' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | リレーション
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }
}
