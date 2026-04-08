<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $application
 * @property string $token_type
 * @property string $scope
 * @property string $access_token
 * @property string $refresh_token
 * @property CarbonImmutable $expired_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OauthToken query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperOauthToken
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

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expired_at);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
        ];
    }
}
