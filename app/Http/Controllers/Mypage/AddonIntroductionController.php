<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class AddonIntroductionController extends ArticleController
{
    protected $post_type = 'addon-introduction';

    protected function saveContents(Request $request, Article $article)
    {
        $contents = $article->contents;
        $attachments = [];

        $contents['author']      = $request->input('author');
        $contents['link']        = $request->input('link');
        $contents['description'] = $request->input('description');
        $contents['thanks']      = $request->input('thanks');
        $contents['license']     = $request->input('license');
        $contents['agreement']   = $request->filled('agreement');

        if ($request->hasFile('thumbnail')) {
            $thumbnail = self::saveAttachment($request->file('thumbnail'), Auth::id(), $article);
            $contents['thumbnail'] = $thumbnail->id;
            $attachments[] = $thumbnail;
        }
        $article->contents = $contents;
        $article->save();
        $article->attachments()->saveMany($attachments);

        $categories = array_merge(
            [$article->category_post->id],
            $request->input('categories.pak', []),
            $request->input('categories.addon', []),
            $request->input('categories.pak128_position', []),
            [$request->input('categories.license')]
        );
        $article->categories()->sync($categories);
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
