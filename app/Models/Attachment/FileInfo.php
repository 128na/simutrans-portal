<?php

declare(strict_types=1);

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Model;

class FileInfo extends Model
{
    protected $fillable = [
        'attachment_id',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];
}
