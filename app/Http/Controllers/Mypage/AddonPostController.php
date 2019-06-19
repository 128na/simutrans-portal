<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class AddonPostController extends ArticleController
{
    protected $post_type = 'addon-post';

    protected function saveContents(Request $request, Article $article)
    {
        $contents = $article->contents;
        $attachments = [];

        $contents['author']      = $request->input('author');
        $contents['description'] = $request->input('description');
        $contents['thanks']      = $request->input('thanks');
        $contents['license']     = $request->input('license');

        if ($request->hasFile('thumbnail')) {
            $thumbnail = self::saveAttachment($request->file('thumbnail'), Auth::id(), $article, $contents['thumbnail'] ?? null);
            $contents['thumbnail'] = $thumbnail->id;
            $attachments[] = $thumbnail;
        }

        if ($request->hasFile('file')) {
            $file = self::saveAttachment($request->file('file'), Auth::id(), $article, $contents['file'] ?? null);
            $contents['file'] = $file->id;
            $attachments[] = $file;
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
        $categories = array_filter($categories); // remove null elements
        $article->categories()->sync($categories);
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'author'      => 'nullable|max:255',
            'file'        => $article ? 'nullable|file' : 'required|file',
            'description' => 'required|string:2048',
            'thanks'      => 'nullable|string:2048',
            'license'     => 'nullable|string:2048',
            'categories.pak.*'             => 'nullable|exists:categories,id',
            'categories.addon.*'           => 'nullable|exists:categories,id',
            'categories.pak128_position.*' => 'nullable|exists:categories,id',
            'categories.license'           => 'nullable|exists:categories,id',
        ]);
    }
}
