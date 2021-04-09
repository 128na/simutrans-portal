<h2 class="section-title">{{ $slot }}</h2>
@if ($items->isNotEmpty())
    <ul>
        @foreach ($items as $item)
            <li class="mb-4">
                <h5>
                    <a href="{{ route('publicBookmarks.show', $item->uuid) }}">{{ $item->title }}</a>
                    <small>
                        ({{ $item->bookmark_items_count }})
                        <span>
                            作成： {{ $item->user->name }}
                        </span>
                    </small>
                </h5>
                @auth
                    <div>
                        @include('parts.add-bookmark', [
                        'bookmarkItemableType' => 'App\Models\User\Bookmark',
                        'bookmarkItemableId' => $item->id])
                    </div>
                @endauth
            </li>
        @endforeach
    </ul>
@else
    <p>{{ $no_item }}</p>
@endif
