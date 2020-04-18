<?php

namespace App\Casts;

use App\Models\Contents\ProfileData;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ToProfileData implements CastsAttributes
{
    /**
     * 指定された値をキャスト
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \App\Address
     */
    public function get($model, $key, $value, $attributes)
    {
        return new ProfileData(json_decode($value, true));
    }

    /**
     * 指定された値を保存用に準備
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \App\Address  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}
