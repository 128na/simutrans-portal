<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTag
 */
final class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'editable',
        'created_by',
        'last_modified_by',
        'last_modified_at',
    ];

    protected $casts = [
        'editable' => 'boolean',
        'last_modified_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany<Article>
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * @return BelongsTo<User,Tag>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User,Tag>
     */
    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    /**
     * @return array{tagId:int,tagName:string}
     */
    public function getInfoLogging(): array
    {
        return [
            'tagId' => $this->id,
            'tagName' => $this->name,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    /**
     * @param  Builder<Tag>  $builder
     */
    public function scopePopular(Builder $builder): void
    {
        $builder->withCount(['articles' => fn ($q) => $q->active()])
            ->orderBy('articles_count', 'desc');
    }
}
