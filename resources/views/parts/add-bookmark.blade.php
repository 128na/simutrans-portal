<form class="form-inline mt-2" action="{{ route('bookmarkItems.store') }}" method="POST">
    @csrf
    <input type="hidden" name="bookmarkItem[bookmark_itemable_type]" value="{{ $bookmarkItemableType }}">
    <input type="hidden" name="bookmarkItem[bookmark_itemable_id]" value="{{ $bookmarkItemableId }}">
    <div class="input-group">
        <select class="custom-select" name="bookmarkItem[bookmark_id]">
            @foreach (Auth::user()->bookmarks as $bookmark)
                <option value="{{ $bookmark->id }}">{{ $bookmark->title }}</option>
            @endforeach
        </select>
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit">{{ $message ?? '追加' }}</button>
        </div>
    </div>
</form>
