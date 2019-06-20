@foreach ($tags as $tag)
<a href="{{ route('tag', $tag) }}" class="mr-1 badge badge-secondary">{{ $tag->name }}</a>
@endforeach
