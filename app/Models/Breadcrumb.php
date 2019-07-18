<?php

namespace App\Models;

class Breadcrumb
{
    public static function forShow($article)
    {
        if($article->post_type === 'addon-introduction' || $article->post_type === 'addon-introduction') {
            return [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('Articles'), 'url' => route('addons.index')],
                ['name' => __('Detail')],
            ];
        }
        if($article->post_type === 'page' && $article->isAnnounce()) {
            return [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('Announces'), 'url' => route('announces.index')],
                ['name' => __('Detail')],
            ];
        }
        if($article->post_type === 'page') {
            return [
                ['name' => __('Top'), 'url' => route('index')],
                ['name' => __('Pages'), 'url' => route('pages.index')],
                ['name' => __('Detail')],
            ];
        }
        return [];
    }

    public static function forList($name)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => __($name)],
        ];
    }
    public static function forTag($name)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => $name],
        ];
    }
    public static function forCategory($category)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => __("category.{$category->type}.{$category->slug}")],
        ];
    }
    public static function forPakAddon($pak, $addon)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => __('category.pak.'.$pak), 'url' => route('category', ['pak', $pak])],
            ['name' => __('category.addon.'.$addon)],
        ];
    }
    public static function forUser($user)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => __('User :name', ['name' => $user->name])],
        ];
    }
    public static function forSearch($word)
    {
        return [
            ['name' => __('Top'), 'url' => route('index')],
            ['name' => __('Search results by :word', ['word' => $word])],
        ];
    }

}
