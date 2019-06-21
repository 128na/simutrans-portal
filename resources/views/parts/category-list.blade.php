@foreach ($categories as $category)
    <a href="{{ route('category', [$category->type, $category->slug]) }}"
        class="mr-1 badge badge-secondary category-{{ $category->type }} category-{{ $category->type }}-{{ $category->slug }}">
            {{ __("category.{$category->type}.{$category->slug}") }}</a>
@endforeach
