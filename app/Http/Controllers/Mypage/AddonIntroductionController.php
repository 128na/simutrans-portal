<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Contents\Content;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Tag;
use Illuminate\Http\Request;

class AddonIntroductionController extends ArticleController
{
    protected $post_type = 'addon-introduction';

    protected function saveContents(Request $request, Article $article)
    {
        $data = [
            'thumbnail' => $request->input('thumbnail_id'),
            'author' => $request->input('author'),
            'link' => $request->input('link'),
            'description' => $request->input('description'),
            'thanks' => $request->input('thanks'),
            'license' => $request->input('license'),
            'agreement' => $request->filled('agreement'),
        ];
        if ($request->filled('thumbnail_id')) {
            $article->attachments()->save(Attachment::findOrFail($request->input('thumbnail_id')));
        }
        $article->contents = Content::createFromType($this->post_type, $data);

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
