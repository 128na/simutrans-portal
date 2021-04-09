<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white border-bottom">
        @foreach ($breadcrumb as $bread)
            @if (isset($bread['url']))
                <li class="breadcrumb-item"><a href="{{ $bread['url'] }}">{{ $bread['name'] }}</a></li>
            @else
                <li class="breadcrumb-item" aria-current="page">{{ $bread['name'] }}</li>
            @endif
        @endforeach
    </ol>
    @auth
        <div class="breadcrumb bg-white">
            @foreach ($breadcrumb as $bread)
                @if (isset($bread['bookmarkItemableType']))
                    <div class="mr-4">
                        @include('parts.add-bookmark', [

                        'message' => "「{$bread['name']}」を追加",
                        'bookmarkItemableType' => $bread['bookmarkItemableType'],
                        'bookmarkItemableId' => $bread['bookmarkItemableId']])
                    </div>
                @endif
            @endforeach
        </div>
    @endauth

</nav>
