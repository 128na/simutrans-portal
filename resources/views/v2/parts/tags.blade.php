@foreach($tags as $tag)
<a href="{{ route('search', ['tagIds' => [$tag->id]]) }}" class="rounded bg-tag px-2.5 py-0.5 text-white inline-block">{{$tag->name}}</a>
@endforeach
