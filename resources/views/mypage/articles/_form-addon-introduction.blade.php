
<div class="form-group">
    <label for="author"><span class="badge badge-danger mr-1">{{ __('message.required') }}</span>{{ __('article.author') }}</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
</div>

<div class="form-group">
    <label for="link"><span class="badge badge-danger mr-1">{{ __('message.required') }}</span>{{ __('article.link') }}</label>
    <input type="url" class="form-control" id="link" name="link" placeholder="Link" value="{{ old('link', $article->link ?? '') }}">
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.description') }}</label>
    <textarea class="form-control" id="description" name="description" rows="8">{!! e(old('description', $article->description ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.thanks') }}</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{!! e(old('thanks', $article->thanks ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.agreement') }}</label>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="agreement" name="agreement"
            value="1" {{ old('agreement', $article->agreement ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="agreement">{{ __('article.ageement-message') }}</label>
    </div>
</div>

@include('mypage.articles._form-addon-common')
