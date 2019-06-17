
<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">Required</span>Author</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
</div>
<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">Required</span>URL</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
</div>
<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">Optional</span>Decription</label>
    <textarea class="form-control" id="description" name="description" rows="10">{{ old('description', $article->description ?? '') }}</textarea>
</div>
<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Agreement</label>
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="agreement">
        <label class="form-check-label" for="agreement">The add-on author has given you permission to introduce add-ons (or the author's own post)</label>
    </div>
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
