<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property int $id
 * @property string $uuid
 * @property string $bulk_zippable_type
 * @property int $bulk_zippable_id
 * @property int $generated ファイル生成済みか 0:未生成,1:生成済み
 * @property string|null $path 生成ファイルのパス
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read Model|\Eloquent $bulkZippable
 * @method static \Database\Factories\BulkZipFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BulkZip query()
 * @mixin \Eloquent
 */
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
        static::creating(function (self $model): void {
            $model->uuid = (string) Str::uuid();
        });
        self::deleting(function ($model): void {
            $model->deleteFileHandler();
        });
    }

    /**
     * @return MorphTo<Model,BulkZip>
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
}
