<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $application
 * @property string $token_type
 * @property string $scope
 * @property string $access_token
 * @property string $refresh_token
 * @property \Carbon\CarbonImmutable $expired_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OauthToken query()
 *
 * @mixin \Eloquent
 */
class OauthToken extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'application';

    protected $fillable = [
        'application',
        'token_type',
        'scope',
        'access_token',
        'refresh_token',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expired_at);
    }
}
