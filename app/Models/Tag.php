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

    /**
     * @return BelongsToMany<Article,$this,\Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * @return BelongsTo<User,$this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User,$this>
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
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function popular(Builder $builder): void
    {
        $builder->withCount(['articles' => fn ($q) => $q->active()])
            ->orderBy('articles_count', 'desc');
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'editable' => 'boolean',
            'last_modified_at' => 'datetime',
        ];
    }
}
