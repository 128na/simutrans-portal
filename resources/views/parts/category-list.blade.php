@foreach ($categories as $category)
    <a href="#" class="badge badge-secondary category-{{ $category->type }} category-{{ $category->type }}-{{ $category->slug }}">{{ $category->name }}</a>
@endforeach
