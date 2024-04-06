<?php

declare(strict_types=1);

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $attachment_id
 * @property array $data
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo query()
 * @mixin \Eloquent
 */
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
