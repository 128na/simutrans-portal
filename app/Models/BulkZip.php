<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperBulkZip
 */
final class BulkZip extends Model
{
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

    /**
     * @return MorphTo<Model,$this>
     */
    public function bulkZippable(): MorphTo
    {
        return $this->morphTo();
    }

    public function deleteFileHandler(): bool
    {
        if ($this->path) {
            return Storage::disk('public')->delete($this->path);
        }

        return false;
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (self $model): void {
            $model->uuid = (string) Str::uuid();
        });
        self::deleting(function (\Illuminate\Database\Eloquent\Model $model): void {
            $model->deleteFileHandler();
        });
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'generated' => 'boolean',
        ];
    }
}
