
<div class="form-group">
    <label for="author"><span class="badge badge-danger mr-1">Required</span>Author</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
</div>

<div class="form-group">
    <label for="link"><span class="badge badge-danger mr-1">Required</span>URL</label>
    <input type="url" class="form-control" id="link" name="link" placeholder="Link" value="{{ old('link', $article->link ?? '') }}">
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">Optional</span>Decription</label>
    <textarea class="form-control" id="description" name="description" rows="8">{{ old('description', $article->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">Optional</span>Thanks</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="4">{{ old('thanks', $article->thanks ?? '') }}</textarea>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Agreement</label>
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="agreement" name="agreement"
            value="1" {{ old('agreement', $article->agreement ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="agreement">The add-on author has given you permission to introduce add-ons (or the author's own post)</label>
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Pak</label>
    <div class="category-list">
        @foreach ($categories->get('pak') as $category)
            @php
                $checked = old('categories.pak.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[pak][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Addon Type</label>
    <div class="category-list">
        @foreach ($categories->get('addon') as $category)
            @php
                $checked = old('categories.addon.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[addon][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Pak128 Position</label>
    <div class="category-list">
        @foreach ($categories->get('pak128_position') as $category)
            @php
                $checked = old('categories.pak128_position.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[pak128_position][{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>License</label>
    <div class="category-list">
        @foreach ($categories->get('license') as $category)
            @php
                $checked = old('categories.license', isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" id="category-{{ $category->id }}" name="categories[license]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label for="license"><span class="badge badge-secondary mr-1">Optional</span>License for Others</label>
    <textarea class="form-control" id="license" name="license" rows="4">{{ old('license', $article->license ?? '') }}</textarea>
</div>
