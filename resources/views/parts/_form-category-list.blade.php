@foreach ($categories as $category)
    @php
        $checked = old("categories.{$name}.{$category->id}", isset($article) ? $article->hasCategory($category->id) : false);
    @endphp
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="category-{{ $category->id }}" name="categories[{{ $name }}][{{ $category->id }}]"
            value="{{ $category->id }}" {{ $checked ? 'checked' : '' }}>
        <label class="custom-control-label" for="category-{{ $category->id }}">{{ __("category.{$category->type}.{$category->slug}") }}</label>
    </div>
@endforeach
