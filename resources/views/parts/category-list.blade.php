@if (isset($post_type) && $post_type)
    <a href="{{ route($route_name) }}"class="badge badge-info text-white">
        {{ __("post_types.{$post_type}") }}</a>
@endif

@foreach ($categories as $category)
    <a href="{{ route('category', [$category->type, $category->slug]) }}"
        class="badge badge-{{$category->type === 'pak' ? 'danger' : 'secondary'}}">
        {{ __("category.{$category->type}.{$category->slug}") }}</a>
@endforeach
