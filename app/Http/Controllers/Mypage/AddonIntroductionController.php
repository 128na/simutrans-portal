<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Attachment;

class AddonIntroductionController extends ArticleController
{
    protected $post_type = 'addon-introduction';

    protected function saveContents(Request $request, Article $article, $attachments)
    {
        $article->setContents('author', $request->input('author'));
        $article->setContents('link', $request->input('link'));
        $article->setContents('description', $request->input('description'));
        $article->setContents('thanks', $request->input('thanks'));
        $article->setContents('license', $request->input('license'));
        $article->setContents('agreement', $request->filled('agreement'));

        $article->save();

        $categories = array_merge(
            [$article->category_post->id],
            $request->input('categories.pak', []),
            $request->input('categories.addon', []),
            $request->input('categories.pak128_position', []),
            [$request->input('categories.license')]
        );
        $categories = array_filter($categories); // remove null elements
        $article->categories()->sync($categories);

        return $attachments;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'author'       => 'required|max:255',
            'link'         => 'required|url',
            'description'  => 'nullable|string:2048',
            'agreement'    => 'nullable',
            'thanks'       => 'nullable|string:2048',
            'license'      => 'nullable|string:2048',
            'categories.pak.*'             => 'nullable|exists:categories,id',
            'categories.addon.*'           => 'nullable|exists:categories,id',
            'categories.pak128_position.*' => 'nullable|exists:categories,id',
            'categories.license'           => 'nullable|exists:categories,id',
        ]);
    }
}
