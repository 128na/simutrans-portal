<?php

declare(strict_types=1);

namespace App\Casts;

use App\Models\User\ProfileData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<ProfileData,string>
 */
class ToProfileData implements CastsAttributes
{
    /**
     * 指定された値をキャスト.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string>  $attributes
     * @return ProfileData
     */
    public function get($model, $key, $value, $attributes)
    {
        return new ProfileData(json_decode((string) $value, true));
    }

    /**
     * 指定された値を保存用に準備.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string>  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value) ?: '';
    }
}
