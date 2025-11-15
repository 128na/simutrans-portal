<?php

declare(strict_types=1);

namespace App\Casts;

use App\Enums\ArticlePostType;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\AddonPostContent;
use App\Models\Contents\MarkdownContent;
use App\Models\Contents\PageContent;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<\App\Models\Contents\Content,\App\Models\Contents\Content>
 */
final class ToArticleContents implements CastsAttributes
{
    /**
     * 指定された値をキャスト.
     *
     * @param  \App\Models\Article  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array<string>  $attributes
     * @return \App\Models\Contents\Content
     */
    #[\Override]
    public function get($model, $key, $value, $attributes): \App\Models\Contents\Content
    {
        /** @var array{thumbnail?:int,sections?:array<int,array{type:string,caption?:string,text?:string,url?:string,id?:int}>,markdown?:string,description?:string,file?:int,author?:string,license?:string,thanks?:string,link?:string,agreement?:bool,exclude_link_check?:bool} */
        $data = json_decode((string) $value, true);

        return match ($model->post_type) {
            ArticlePostType::AddonIntroduction => new AddonIntroductionContent($data),
            ArticlePostType::AddonPost => new AddonPostContent($data),
            ArticlePostType::Page => new PageContent($data),
            ArticlePostType::Markdown => new MarkdownContent($data),
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
    #[\Override]
    public function set($model, $key, $value, $attributes): string
    {
        $encoded = json_encode($value);
        return $encoded !== false ? $encoded : '';
    }
}
