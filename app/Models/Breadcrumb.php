<?php

namespace App\Models;

class Breadcrumb
{
    public static function forShow($article)
    {
        if($article->post_type === 'addon-introduction' || $article->post_type === 'addon-introduction') {
            return [
                ['name' => __('message.top'), 'url' => route('index')],
                ['name' => __('message.articles'), 'url' => route('addons.index')],
                ['name' => __('message.detail')],
            ];
        }
        if($article->post_type === 'page' && $article->isAnnounce()) {
            return [
                ['name' => __('message.top'), 'url' => route('index')],
                ['name' => __('message.announces'), 'url' => route('announces.index')],
                ['name' => __('message.detail')],
            ];
        }
        if($article->post_type === 'page') {
            return [
                ['name' => __('message.top'), 'url' => route('index')],
                ['name' => __('message.pages'), 'url' => route('pages.index')],
                ['name' => __('message.detail')],
            ];
        }
        return [];
    }

    public static function forList($name)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => __('message.'.$name)],
        ];
    }
    public static function forTag($name)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => $name],
        ];
    }
    public static function forCategory($category)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => __("category.{$category->type}.{$category->slug}")],
        ];
    }
    public static function forPakAddon($pak, $addon)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => __('category.pak.'.$pak), 'url' => route('category', ['pak', $pak])],
            ['name' => __('category.addon.'.$addon)],
        ];
    }
    public static function forUser($user)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => __('message.title-of-user', ['name' => $user->name])],
        ];
    }
    public static function forSearch($word)
    {
        return [
            ['name' => __('message.top'), 'url' => route('index')],
            ['name' => $word],
        ];
    }

}
