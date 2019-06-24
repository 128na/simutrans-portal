{{-- 共通フォーム --}}
<div class="form-group">
    <label for="status"><span class="badge badge-danger mr-1">{{__('message.required') }}</span>{{ __('article.status') }}</label>
    <select class="form-control" id="status" name="status">
        @foreach (config('status', []) as $key => $name)
            <option value="{{ $key }}" {{ old('status', $article->status ?? config('status.draft')) === $key ? 'selected' : '' }}>{{ __('status.'.$key) }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">{{__('message.required') }}</span>{{ __('article.title') }}</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{ old('title', $article->title ?? '') }}">
</div>

<div class="form-group">
    <label for="slug"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.slug') }}</label>
    <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="{{ old('slug', $article->slug ?? '') }}">
    <small class="form-text text-muted">{{ __('article.slug-memo') }}</small>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.thumbnail-image') }} </label>
    <div class="mb-2">
        <img id="thumbnail_preview" class="preview img-thumbnail " src="{{ old('thumbnail_preview_url', $article->thumbnail_url ?? asset('storage/'.config('attachment.no-thumbnail'))) }}">
        <input type="hidden" id="thumbnail_preview_url" name="thumbnail_preview_url" value="{{ old('thumbnail_preview_url') }}">
        <input type="hidden" id="thumbnail_id" name="thumbnail_id" value="{{ old('thumbnail_id', $article->thumbnail_id ?? '') }}">
    </div>
    <div>
        <a href="#" class="btn btn-secondary js-open-uploader"
            data-input="#thumbnail_id" data-preview="#thumbnail_preview" data-preview-url="#thumbnail_preview_url" data-only-image="true"
        >{{ __('message.open-uploader') }}</a>
    </div>
</div>
