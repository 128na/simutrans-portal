<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Contents\Content;
use App\Models\Article;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarkdownController extends ArticleController
{
    protected $post_type = 'page';

    protected function saveContents(Request $request, Article $article)
    {
        $data = [
            'thumbnail' => $request->input('thumbnail_id'),
        ];
        if ($request->filled('thumbnail_id')) {
            $article->attachments()->save(Attachment::findOrFail($request->input('thumbnail_id')));
        }
        $data['data'] = $request->input('data');
        $article->contents = Content::createFromType($this->post_type, $data);

        $categories = $request->input('categories.page', []);
        $article->categories()->sync($categories);

        return $article;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'data' => 'required|string',
            'categories.page.*'  => 'nullable|exists:categories,id,type,page',
        ]);
    }
}
