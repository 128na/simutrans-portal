<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkZip extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'bulk_zippable_id',
        'bulk_zippable_type',
        'generated',
        'path',
    ];
    protected $hidden = [
        'path',
    ];

    protected static function booted()
    {
        static::creating(function (self $model) {
            $model->uuid = (string) Str::uuid();
        });
        self::deleting(function ($model) {
            $model->deleteFileHandler();
        });
    }

    public function bulkZippable(): MorphTo
    {
        return $this->morphTo();
    }

    public function deleteFileHandler()
    {
        if ($this->path) {
            return Storage::disk('public')->delete($this->path);
        }
    }
}
