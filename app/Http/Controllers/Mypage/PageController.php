<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends ArticleController
{
    protected $post_type = 'page';

    protected function saveContents(Request $request, Article $article, $attachments)
    {
        $old_sections = $article->getContents('sections', []);
        $sections = collect($request->input('sections', []));

        $sections = $sections->map(function($section, $index) use ($request, $article, &$attachments) {
            switch ($section['type']) {
                case 'caption':
                    return [
                        'type' => 'caption',
                        'caption' => $section['caption'],
                    ];
                case 'text':
                    return [
                        'type' => 'text',
                        'text' => $section['text'],
                    ];
                case 'image':
                    if($request->hasFile("sections.{$index}.image")) {
                        $image = static::saveAttachment(
                            $request->file("sections.{$index}.image"), Auth::id(), $article, $section['id'] ?? null);
                        $article->setContents('image', $image->id);
                        $attachments[] = $image;

                        return [
                            'type' => 'image',
                            'id'   => $image->id,
                        ];
                    } else {
                        return [
                            'type' => 'image',
                            'id'   => (int)$section['id'],
                        ];
                    }
            }
        });

        $article->setContents('sections', $sections->toArray());

        $article->save();
        return $attachments;
    }

    protected static function getValidateRule($article = null)
    {
        return array_merge(parent::getValidateRule($article), [
            'sections' => 'required|array',
            'sections.*.type'    => 'required|in:caption,text,image',
            'sections.*.caption' => 'required_if:sections.*.type,caption|max:255',
            'sections.*.text'    => 'required_if:sections.*.type,text|max:1024',
            'sections.*.image'   => 'nullable|image',
            'sections.*.id'      => 'nullable|numeric',
        ]);
    }
}
