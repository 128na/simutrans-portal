<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Contents\Content;
use App\Models\Article;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends ArticleController
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

        $sections = collect($request->input('sections', []));
        $data['sections'] = $sections->map(function($section, $index) use ($request, $article) {
            switch ($section['type']) {
                case 'caption':
                    return [
                        'type'    => 'caption',
                        'caption' => $section['caption'],
                    ];
                case 'text':
                    return [
                        'type' => 'text',
                        'text' => $section['text'],
                    ];
                case 'image':
                    $article->attachments()->save(
                        Attachment::findOrFail($section['id']));
                    return [
                        'type' => 'image',
                        'id'   => (int)$section['id'],
                    ];
            }
        });
        $article->contents = Content::createFromType($this->post_type, $data);

        $categories = $request->input('categories.page', []);
        $article->categories()->sync($categories);

        return $article;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'sections' => 'required|array|min:1',
            'sections.*.type'    => 'required|in:caption,text,image',
            'sections.*.caption' => 'required_if:sections.*.type,caption|max:255',
            'sections.*.text'    => 'required_if:sections.*.type,text|max:2048',
            'sections.*.id'      => 'required_if:sections.*.type,image|exists:attachments,id,user_id,'.Auth::id(),
            'categories.page.*'  => 'nullable|exists:categories,id,type,page',
        ]);
    }
}
