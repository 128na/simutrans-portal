<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-white border-bottom">
        @foreach ($breadcrumb as $bread)
            @if (isset($bread['url']))
                <li class="breadcrumb-item">
                    <a href="{{ $bread['url'] }}">{{ $bread['name'] }}</a>
                @else
                <li class="breadcrumb-item" aria-current="page">
                    {{ $bread['name'] }}
            @endif
            </li>
        @endforeach
    </ol>
</nav>
