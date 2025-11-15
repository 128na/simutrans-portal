<?php

declare(strict_types=1);

namespace App\Casts;

use App\Models\User\ProfileData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<ProfileData,ProfileData>
 */
final class ToProfileData implements CastsAttributes
{
    /**
     * 指定された値をキャスト.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array<string>  $attributes
     * @return ProfileData
     */
    #[\Override]
    public function get(
        \Illuminate\Database\Eloquent\Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): ProfileData {
        /** @var array{avatar?: int, description?: string, website?: string} */
        $data = json_decode((string) $value, true);

        return new ProfileData($data);
    }

    /**
     * 指定された値を保存用に準備.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  ProfileData  $value
     * @param  array<string>  $attributes
     * @return string
     */
    #[\Override]
    public function set(
        \Illuminate\Database\Eloquent\Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): string {
        $encoded = json_encode($value);
        return $encoded !== false ? $encoded : '';
    }
}
