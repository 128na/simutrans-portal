<h2 class="section-title">{{ $slot }}</h2>
@if ($items->isNotEmpty())
    <ul class="list-style-none">
        @foreach ($items as $item)
            <li class="mb-4">
                <strong>
                    <a href="{{ route('publicBookmarks.show', $item->uuid) }}">
                        {{ $item->title }}
                        ({{ $item->bookmark_items_count }})
                    </a>
                    @auth
                        @include('parts.add-bookmark', [
                        'name' => $item->title,
                        'type' => 'App\Models\User\Bookmark',
                        'id' => $item->id])
                    @endauth
                    <small>
                        <span>
                            作成： {{ $item->user->name }}
                        </span>
                    </small>
                </strong>
            </li>
        @endforeach
    </ul>
@else
    <p>{{ $no_item }}</p>
@endif
