@foreach ($categories as $category)
    <a href="{{ route('category', [$category->type, $category->slug]) }}"
        class="badge badge-secondary">
        @lang("category.{$category->type}.{$category->slug}")</a>
@endforeach
