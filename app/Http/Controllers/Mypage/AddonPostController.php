<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Article;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddonPostController extends ArticleController
{
    protected $post_type = 'addon-post';

    protected function saveContents(Request $request, Article $article)
    {
        $article->setContents('author', $request->input('author', Auth::user()->name));
        $article->setContents('description', $request->input('description'));
        $article->setContents('thanks', $request->input('thanks'));
        $article->setContents('license', $request->input('license'));

        $article->setContents('file', $request->input('file_id'));
        if ($request->filled('file_id')) {
            $article->attachments()->save(Attachment::findOrFail($request->input('file_id')));
        }

        $categories = array_merge(
            $request->input('categories.pak', []),
            $request->input('categories.addon', []),
            $request->input('categories.pak128_position', []),
            $request->input('categories.license', [])
        );
        $categories = array_filter($categories); // remove null elements
        $article->categories()->sync($categories);

        return $article;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'author'      => 'nullable|max:255',
            'file_id'     => 'required|exists:attachments,id,user_id,'.Auth::id(),
            'description' => 'required|string|max:2048',
            'thanks'      => 'nullable|string|max:2048',
            'license'     => 'nullable|string|max:2048',
            'categories.pak.*'             => 'exists:categories,id,type,pak',
            'categories.addon.*'           => 'exists:categories,id,type,addon',
            'categories.pak128_position.*' => 'exists:categories,id,type,pak128_position',
            'categories.license'           => 'exists:categories,id,type,license',
            'tags.*'                       => 'max:255',
        ]);
    }
}
