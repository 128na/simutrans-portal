<div class="form-group">
    <label for="author"><span class="badge badge-danger mr-1">@lang('Required')</span>@lang('Author')</label>
    <input type="text" class="form-control" id="author" name="author" value="{{ old('author', $article->author ?? '') }}">
</div>

<div class="form-group">
    <label for="link"><span class="badge badge-danger mr-1">@lang('Required')</span>@lang('Link')</label>
    <input type="url" class="form-control" id="link" name="link" value="{{ old('link', $article->link ?? '') }}">
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Description')</label>
    <textarea class="form-control" id="description" name="description" rows="8">{!! e(old('description', $article->description ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Acknowledgments and Referenced')</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{!! e(old('thanks', $article->thanks ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Agreement')</label>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="agreement" name="agreement"
            value="1" {{ old('agreement', $article->agreement ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="agreement">@lang('This article is published by author\'s permission or by author himself.')</label>
    </div>
</div>

@include('parts._form-addon-common')
