
<div class="form-group">
    <label for="author"><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Author')</label>
    <input type="text" class="form-control" id="author" name="author" value="{{ old('author', $article->author ?? '') }}">
    <small class="form-text text-muted">@lang('If empty, the username is used.')</small>
</div>

<div class="form-group">
    <label><span class="badge badge-danger mr-1">@lang('Required')</span>@lang('Addon File') </label>
    <div class="mb-2">
        <p id="file_preview">
            {{ $article->file->original_name ?? __('Not selected.') }}
        </p>
        <input type="hidden" id="file_id" name="file_id" value="{{ old('file_id', isset($article) ? $article->getContents('file') : '') }}">
    </div>
    <div>
        <a href="#" class="btn btn-secondary js-open-uploader"
            data-input="#file_id" data-preview="#file_preview" data-preview-url=""
        >@lang('Open File Manager')</a>
    </div>
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-danger mr-1">@lang('Required')</span>@lang('Description')</label>
    <textarea class="form-control" id="description" name="description" rows="8">{!! e(old('description', $article->description ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Acknowledgments and Referenced')</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{!! e(old('thanks', $article->thanks ?? '')) !!}</textarea>
</div>

@include('parts._form-addon-common')
