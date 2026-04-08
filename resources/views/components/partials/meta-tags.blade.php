<meta
  name="twitter:card"
  content="{{ $meta['card_type'] ?? 'summary_large_image' }}"
/>
<meta name="twitter:site" content="{{ '@'.config('app.twitter') }}" />
<meta name="twitter:creator" content="{{ '@'.config('app.creator') }}" />
<meta
  property="og:image"
  content="{{ $meta['image'] ?? asset(config('app.meta-image')) }}"
/>
<meta property="og:type" content="website" />
<meta
  property="og:title"
  content="{{ $meta['title'] ?? config('app.name') }}"
/>
<meta
  property="og:description"
  content="{{ $meta['description'] ?? config('app.meta-description') }}"
/>
<meta
  property="og:url"
  content="{{ $meta['canonical'] ?? url()->current() }}"
/>

{{-- 本番環境以外はnoindex nofollow --}}
@unless (\Illuminate\Support\Facades\App::environment('production'))
  <meta name="robots" content="noindex, nofollow" />
@endunless

<link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}" />
<link rel="icon" type="image/ico" href="/favicon.ico" />
