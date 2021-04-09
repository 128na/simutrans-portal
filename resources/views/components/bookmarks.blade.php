<h2 class="section-title">{{ $slot }}</h2>
@if ($items->isNotEmpty())
    <div class="bookmarks">
        @foreach ($items as $item)
            <div>
                <div>
                    {{ $item->user->name }}
                </div>
                <a href="{{ route('publicBookmarks.show', $item->uuid) }}">{{ $item->title }}
                    ({{ $item->bookmark_items_count }})</a>
                <div>
                    {{ $item->memo }}
                </div>
                @auth
                    <div>
                        @include('parts.add-bookmark', [
                        'bookmarkItemableType' => 'App\Models\Article',
                        'bookmarkItemableId' => $item->id])
                    </div>
                @endauth
            </div>
        @endforeach
    </div>
@else
    <p>{{ $no_item }}</p>
@endif
