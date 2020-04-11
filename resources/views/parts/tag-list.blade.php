@foreach ($tags as $tag)
    <a href="{{ route('tag', $tag) }}" class="badge badge-secondary">{{ $tag->name }}</a>
@endforeach
