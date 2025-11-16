@switch(true)
{{-- SNS --}}
@case(str_starts_with($website, 'https://twitter.com/') || str_starts_with($website, 'https://x.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/twitter.svg') }}" alt="Twitter" title="Twitter" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://misskey.io/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/misskey.svg') }}" alt="Misskey" title="Misskey" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://bsky.app/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/bluesky.svg') }}" alt="Bluesky" title="Bluesky" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://discord.gg/')|| str_starts_with($website, 'https://discord.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/discord.svg') }}" alt="Discord" title="Discord" class="inline-block h-[1em] align-text-bottom">
</a>
@break
{{-- 動画サイト --}}
@case(str_starts_with($website, 'https://www.youtube.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/youtube.png') }}" alt="YouTube" title="YouTube" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@case(str_starts_with($website, 'https://www.nicovideo.jp/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/niconico.png') }}" alt="Niconico" title="Niconico" class="inline-block h-[1em] align-text-bottom">
</a>
@break
{{-- その他 --}}
@case(str_starts_with($website, 'https://github.com/'))
<a href="{{ $website }}" target="_blank" rel="noopener noreferrer">
    <img src="{{ asset('storage/social/github.svg') }}" alt="GitHub" title="GitHub" class="inline-block h-[1em] align-text-bottom">
</a>
@break
@break
@default
@include('v2.parts.link-external', ['url' => $website, 'title' => parse_url($website, PHP_URL_HOST)])
@endswitch
