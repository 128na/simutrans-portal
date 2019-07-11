<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Tag;
use Illuminate\Http\Request;

class AddonIntroductionController extends ArticleController
{
    protected $post_type = 'addon-introduction';

    protected function saveContents(Request $request, Article $article)
    {
        $article->setContents('author', $request->input('author'));
        $article->setContents('link', $request->input('link'));
        $article->setContents('description', $request->input('description'));
        $article->setContents('thanks', $request->input('thanks'));
        $article->setContents('license', $request->input('license'));
        $article->setContents('agreement', $request->filled('agreement'));

        $categories = array_merge(
            $request->input('categories.pak', []),
            $request->input('categories.addon', []),
            $request->input('categories.pak128_position', []),
            $request->input('categories.license', [])
        );
        $categories = array_filter($categories); // remove null elements
        $article->categories()->sync($categories);

        $tag_names = collect($request->input('tags', []));
        $tags = $tag_names->map(function($tag_name) {
            return Tag::firstOrCreate(['name' => $tag_name]);
        });
        $article->tags()->sync($tags->pluck('id')->toArray());

        return $article;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'author'       => 'required|max:255',
            'link'         => 'required|url|max:255',
            'description'  => 'nullable|string|max:2048',
            'agreement'    => 'nullable',
            'thanks'       => 'nullable|string|max:2048',
            'license'      => 'nullable|string|max:2048',
            'categories.pak.*'             => 'exists:categories,id,type,pak',
            'categories.addon.*'           => 'exists:categories,id,type,addon',
            'categories.pak128_position.*' => 'exists:categories,id,type,pak128_position',
            'categories.license'           => 'exists:categories,id,type,license',
            'tags.*'                       => 'max:255',
        ]);
    }
}
