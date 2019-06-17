
<div class="form-group">
    <label for="title"><span class="badge badge-secondary mr-1">Optional</span>Author</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
    <small class="form-text text-muted">If empty, use the your user name.</small>
</div>
<div class="form-group">
    <label><span class="badge badge-danger mr-1">Required</span>Addon File</label>
    <div class="custom-file">
        <label class="custom-file-label" for="file">{{ old('title', $article->file->original_name ?? 'Choose archive or addon file') }}</label>
        <input type="file" class="custom-file-input js-preview-trigger" id="file" name="file">
    </div>
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-danger mr-1">Required</span>Decription</label>
    <textarea class="form-control" id="description" name="description" rows="10">{{ old('title', $article->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label for="license"><span class="badge badge-secondary mr-1">Optional</span>License</label>
    <textarea class="form-control" id="license" name="license" rows="10">{{ old('license', $article->license ?? '') }}</textarea>
</div>

<div class="form-group">
    <label for="thanks"><span class="badge badge-secondary mr-1">Optional</span>Thanks</label>
    <textarea class="form-control" id="thanks" name="thanks" rows="10">{{ old('thanks', $article->thanks ?? '') }}</textarea>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Pak</label>
    <div class="category-list">
        @foreach ($categories->get('pak') as $category)
            @php
                $checked = old('categories.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="pak-{{ $category->id }}" name="categories[{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="pak-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Addon Type</label>
    <div class="category-list">
        @foreach ($categories->get('addon') as $category)
            @php
                $checked = old('categories.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="pak-{{ $category->id }}" name="categories[{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="pak-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Pak128 Position</label>
    <div class="category-list">
        @foreach ($categories->get('pak128_position') as $category)
            @php
                $checked = old('categories.'.$category->id, isset($article) ? $article->hasCategory($category->id) : false);
            @endphp
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="pak-{{ $category->id }}" name="categories[{{ $category->id }}]"
                    value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
                <label class="custom-control-label" for="pak-{{ $category->id }}">{{ $category->name }}</label>
            </div>
        @endforeach
    </div>
</div>
