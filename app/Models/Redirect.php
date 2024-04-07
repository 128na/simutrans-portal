<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRedirect
 */
final class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
    ];

    /*
    |--------------------------------------------------------------------------
    | スコープ
    |--------------------------------------------------------------------------
    */
    /**
     * @param  Builder<Redirect>  $builder
     */
    public function scopeFrom(Builder $builder, string $from): void
    {
        $builder->where('from', $from);
    }
}
