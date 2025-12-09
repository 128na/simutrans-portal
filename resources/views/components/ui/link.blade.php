@php
// .v2-link:not([href^="https://simutrans-portal.128-bit.net"]):not([href^="http://localhost:1080"]):not([href^="#"]):not([href^="/"]) 相当
$external = !(str_starts_with($url, config('app.url')) || str_starts_with($url, '/') || str_starts_with($url, '#'));
@endphp
<a href="{{$url}}" {{ $external ? 'target="_blank" rel="noopener noreferrer"' : '' }} class="v2-link">
    {{$title ?? $url}}
</a>
