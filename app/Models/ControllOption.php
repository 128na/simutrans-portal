<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ControllOptionKey;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ControllOptionKey $key
 * @property bool $value
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ControllOption query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperControllOption
 */
class ControllOption extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'value',
    ];

    public function restrictLogin(): bool
    {
        return $this->isRestrict(ControllOptionKey::Login);
    }

    public function restrictRegister(): bool
    {
        return $this->isRestrict(ControllOptionKey::Register);
    }

    public function restrictInvitationCode(): bool
    {
        return $this->isRestrict(ControllOptionKey::InvitationCode);
    }

    public function restrictArticleUpdate(): bool
    {
        return $this->isRestrict(ControllOptionKey::ArticleUpdate);
    }

    public function restrictTagUpdate(): bool
    {
        return $this->isRestrict(ControllOptionKey::TagUpdate);
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'key' => ControllOptionKey::class,
            'value' => 'bool',
        ];
    }

    private function isRestrict(ControllOptionKey $controllOptionKey): bool
    {
        $option = self::where('key', $controllOptionKey)->first();

        // 行が存在しない場合は安全側に倒して制限する（fail-closed）。
        if ($option === null) {
            return true;
        }

        return ! $option->value;
    }
}
