
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
    <textarea class="form-control" id="description" name="description" rows="8">{{ old('description', $article->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.thanks') }}</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{{ old('thanks', $article->thanks ?? '') }}</textarea>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.agreement') }}</label>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="agreement" name="agreement"
            value="1" {{ old('agreement', $article->agreement ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="agreement">{{ __('article.ageement-message') }}</label>
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.pak') }}</label>
    <div class="category-list">
        @foreach ($paks as $category)
            @php
                $checked = old('categories.pak.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[pak][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ __("category.{$category->type}.{$category->slug}") }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.addon-type') }}</label>
    <div class="category-list">
        @foreach ($addons as $category)
            @php
                $checked = old('categories.addon.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[addon][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ __("category.{$category->type}.{$category->slug}") }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.pak128-position') }}</label>
    <div class="category-list">
        @foreach ($pak128_positions as $category)
            @php
                $checked = old('categories.pak128_position.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[pak128_position][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ __("category.{$category->type}.{$category->slug}") }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.license') }}</label>
    <div class="category-list">
        @foreach ($licenses as $category)
            @php
                $checked = old('categories.license', isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" id="category-{{ $category->id }}" name="categories[license]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ __("category.{$category->type}.{$category->slug}") }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label for="license"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.license-other') }}</label>
    <textarea class="form-control" id="license" name="license" rows="4">{{ old('license', $article->license ?? '') }}</textarea>
</div>
