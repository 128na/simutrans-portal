<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
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

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expired_at);
    }
}
