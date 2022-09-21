<?php

namespace App\Casts;

use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\AddonPostContent;
use App\Models\Contents\MarkdownContent;
use App\Models\Contents\PageContent;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ToArticleContents implements CastsAttributes
{
    /**
     * 指定された値をキャスト.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param mixed                               $value
     * @param array                               $attributes
     *
     * @return \App\Models\Contents\Content
     */
    public function get($model, $key, $value, $attributes)
    {
        $value = json_decode($value, true);
        switch ($model->post_type) {
            case 'addon-introduction':
                return new AddonIntroductionContent($value);
            case 'addon-post':
                return new AddonPostContent($value);
            case 'page':
                return new PageContent($value);
            case 'markdown':
                return new MarkdownContent($value);
        }
    }

    /**
     * 指定された値を保存用に準備.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     * @param \App\Models\Contents\Content        $value
     * @param array                               $attributes
     *
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}
