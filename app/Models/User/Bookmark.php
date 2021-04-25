<?php

namespace App\Models\User;

use App\Models\BulkZip;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'is_public',
        'title',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function (self $model) {
            $model->uuid = (string) Str::uuid();
        });
        // 論理削除されていないユーザーを持つ
        static::addGlobalScope('WithoutTrashedUser', function (Builder $builder) {
            $builder->has('user');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkItems(): HasMany
    {
        return $this->hasMany(BookmarkItem::class)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc');
    }

    public function bookmarkItemables(): MorphMany
    {
        return $this->morphMany(BookmarkItem::class, 'bookmark_itemable');
    }

    public function bulkZippable(): MorphOne
    {
        return $this->morphOne(BulkZip::class, 'bulk_zippable');
    }
}
