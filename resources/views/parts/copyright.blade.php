<li class="nav-item">
    <small class="d-block mb-1 text-white">
        <a class="text-white" href="{{ url('/articles/about') }}">サイトの使い方</a><br>
        <a class="text-white" href="{{ url('/articles/privacy') }}">プライバシーポリシー</a>
    </small>

    <small class="d-block mb-1 text-white">
        {{ config('app.name') }}
        v{{ config('app.version') }}
    </small>
    <small class="d-block mb-1 text-white">
        © 2020 <a class="text-white" href="https://twitter.com/128Na" target="_blank" rel="noopener nofollow">@128Na</a>
        /
        <a class="text-white" href="https://github.com/128na/simutrans-portal" target="_blank"
            rel="noopener nofollow">GitHub</a>
    </small>
    <small class="d-block mb-1 text-white">
        <a class="text-white" href="{{ route('feeds.addon') }}">
            <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url('default/feed.png') }}"
                class="feed-icon mr-1">Atom</a>
    </small>
</li>
