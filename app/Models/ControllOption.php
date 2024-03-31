<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ControllOptionKey;
use Illuminate\Database\Eloquent\Model;

class ControllOption extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'key' => ControllOptionKey::class,
        'value' => 'bool',
    ];

    private function isRestrict(ControllOptionKey $controllOptionKey): bool
    {
        return $this->where(['key' => $controllOptionKey, 'value' => 0])->exists();
    }

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

    public function restrictScreenshptUpdate(): bool
    {
        return $this->isRestrict(ControllOptionKey::ScreenshotUpdate);
    }
}
