<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompressedImage extends Model
{
    protected $fillable = [
        'path',
    ];

    public static function isCompressed($path)
    {
        return self::where('path', $path)->exists();
    }
}
