
<div class="form-group">
    <label for="author"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.author') }}</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
    <small class="form-text text-muted">{{ __('article.author-memo') }}</small>
</div>

<div class="form-group">
    <label><span class="badge badge-danger mr-1">{{ __('message.required') }}</span>{{ __('article.addon-file') }} </label>
    <div class="mb-2">
        <p id="file_preview">
            {{ $article->file->original_name ?? __('message.not-selected') }}
        </p>
        <input type="hidden" id="file_id" name="file_id" value="{{ old('file_id', $article->file_id ?? '') }}">
    </div>
    <div>
        <a href="#" class="btn btn-secondary js-open-uploader"
            data-input="#file_id" data-preview="#file_preview" data-preview-url=""
        >{{ __('message.open-uploader') }}</a>
    </div>
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-danger mr-1">{{ __('message.required') }}</span>{{ __('article.description') }}</label>
    <textarea class="form-control" id="description" name="description" rows="8">{!! e(old('description', $article->description ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.thanks') }}</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{!! e(old('thanks', $article->thanks ?? '')) !!}</textarea>
</div>

@include('parts._form-addon-common')
