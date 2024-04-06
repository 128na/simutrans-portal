<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $from リダイレクト元
 * @property string $to リダイレクト先
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 *
 * @method static \Database\Factories\RedirectFactory factory($count = null, $state = [])
 * @method static Builder|Redirect from(string $from)
 * @method static Builder|Redirect newModelQuery()
 * @method static Builder|Redirect newQuery()
 * @method static Builder|Redirect query()
 *
 * @mixin \Eloquent
 */
class Redirect extends Model
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
