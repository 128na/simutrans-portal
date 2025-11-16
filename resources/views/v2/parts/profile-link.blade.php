@switch(true)
@case(str_starts_with($website, 'https://twitter.com/') || str_starts_with($website, 'https://x.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/twitter.svg') }}" alt="Twitter" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://misskey.io/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/misskey.svg') }}" alt="Misskey" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://bsky.app/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/bluesky.svg') }}" alt="Bluesky" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://www.youtube.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/youtube.png') }}" alt="YouTube" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://www.nicovideo.jp/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/niconico.png') }}" alt="Niconico" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://github.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/github.svg') }}" alt="GitHub" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@default
@include('v2.parts.link-external', ['url' => $website, 'title' => str_replace('https://', '',$website)])
@endswitch
