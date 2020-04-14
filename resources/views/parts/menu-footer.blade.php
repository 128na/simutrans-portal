<footer class="navbar-dark bg-primary py-1">
    <div class="container d-flex text-white flex-wrap">
        <small class="mb-1 mr-2 text-white">{{ config('app.name') }} {{ config('app.version') }} © 2020 128Na</small>
        <small class="mb-1 mr-2"><a class="text-white" href="https://twitter.com/128Na" target="_blank" rel="noopener, nofollow">Twitter</a></small>
        <small class="mb-1 flex-fill"><a class="text-white" href="https://github.com/128na/simutrans-portal" target="_blank" rel="noopener, nofollow">GitHub</a></small>
        <small class="mb-1"><a class="text-white" href="{{ route('feeds.addon') }}" class="ml-2"><img src="{{ asset('storage/default/feed.png') }}" class="feed-icon">Atom</a></small>
    </div>
</footer>
