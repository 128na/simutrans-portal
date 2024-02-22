<?php

declare(strict_types=1);

namespace App\Casts;

use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\AddonPostContent;
use App\Models\Contents\MarkdownContent;
use App\Models\Contents\PageContent;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ToArticleContents implements CastsAttributes
{
    /**
     * 指定された値をキャスト.
     *
     * @param  \App\Models\Article  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string>  $attributes
     * @return \App\Models\Contents\Content
     */
    public function get($model, $key, $value, $attributes)
    {
        $value = json_decode((string) $value, true);

        return match ($model->post_type) {
            'addon-introduction' => new AddonIntroductionContent($value),
            'addon-post' => new AddonPostContent($value),
            'page' => new PageContent($value),
            'markdown' => new MarkdownContent($value),
            default => throw new Exception('invalid post type'),
        };
    }

    /**
     * 指定された値を保存用に準備.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \App\Models\Contents\Content  $value
     * @param  array<string>  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value) ?: '';
    }
}
