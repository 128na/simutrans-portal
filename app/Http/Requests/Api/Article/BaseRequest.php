<?php

namespace App\Http\Requests\Api\Article;

use App\Rules\ImageAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

abstract class BaseRequest extends FormRequest
{
    /**
     * @return array<mixed>
     */
    public function rules()
    {
        $post_type = request()->input('article.post_type');
        switch ($post_type) {
            case 'addon-post':
                return array_merge($this->baseRule(), $this->addonPost());
            case 'addon-introduction':
                return array_merge($this->baseRule(), $this->addonIntroductiuon());
            case 'page':
                return array_merge($this->baseRule(), $this->page());
            case 'markdown':
                return array_merge($this->baseRule(), $this->markdown());
            default:
                return $this->baseRule();
        }
    }

    /**
     * @return array<mixed>
     */
    abstract protected function baseRule(): array;

    /**
     * @return array<mixed>
     */
    protected function addonPost(): array
    {
        return [
            'article.categories' => 'present|array',
            'article.categories.*.id' => 'required|exists:categories,id',
            'article.tags' => 'present|array',
            'article.tags.*.id' => 'required|exists:tags,id',
            'article.contents' => 'required|array',
            'article.contents.thumbnail' => ['nullable', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
            'article.contents.author' => 'nullable|max:255',
            'article.contents.file' => 'required|exists:attachments,id,user_id,'.Auth::id(),
            'article.contents.description' => 'required|string|max:2048',
            'article.contents.thanks' => 'nullable|string|max:2048',
            'article.contents.license' => 'nullable|string|max:2048',
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function addonIntroductiuon(): array
    {
        return [
            'article.categories' => 'present|array',
            'article.categories.*.id' => 'required|exists:categories,id',
            'article.tags' => 'present|array',
            'article.tags.*.id' => 'required|exists:tags,id',
            'article.contents' => 'required|array',
            'article.contents.thumbnail' => ['nullable', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
            'article.contents.author' => 'required|max:255',
            'article.contents.link' => 'required|url|max:255',
            'article.contents.description' => 'required|string|max:2048',
            'article.contents.agreement' => 'nullable',
            'article.contents.exclude_link_check' => 'nullable',
            'article.contents.thanks' => 'nullable|string|max:2048',
            'article.contents.license' => 'nullable|string|max:2048',
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function page(): array
    {
        return [
            'article.categories' => 'present|array',
            'article.categories.*.id' => 'required|exists:categories,id,type,page',
            'article.contents' => 'required|array',
            'article.contents.thumbnail' => ['nullable', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
            'article.contents.sections' => 'required|array|min:1',
            'article.contents.sections.*.type' => 'required|in:caption,text,url,image',
            'article.contents.sections.*.caption' => 'required_if:sections.*.type,caption|string|max:255',
            'article.contents.sections.*.text' => 'required_if:sections.*.type,text|string|max:2048',
            'article.contents.sections.*.url' => 'required_if:sections.*.type,url|url|max:255',
            'article.contents.sections.*.id' => ['required_if:sections.*.type,image', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
        ];
    }

    /**
     * @return array<mixed>
     */
    protected function markdown(): array
    {
        return [
            'article.categories' => 'present|array',
            'article.categories.*.id' => 'required|exists:categories,id,type,page',
            'article.contents' => 'required|array',
            'article.contents.thumbnail' => ['nullable', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
            'article.contents.markdown' => 'required|string|max:65535',
        ];
    }
}
