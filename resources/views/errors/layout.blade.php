<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $meta['title'] ?? config('app.name') }}</title>

    @production
    @include('ga')
    @endproduction

    <meta name="twitter:card" content="{{ $meta['card_type'] ?? 'summary_large_image' }}">
    <meta name="twitter:site" content="{{ '@' . config('app.twitter') }}">
    <meta name="twitter:creator" content="{{ '@' . config('app.creator') }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset(config('app.meta-image')) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['title'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $meta['description'] ?? config('app.meta-description') }}">
    <meta property="og:url" content="{{ $meta['canonical'] ?? url()->current() }}">

    {{-- 本番環境以外はnoindex nofollow --}}
    @unless (\Illuminate\Support\Facades\App::environment('production'))
    <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
    <link rel=icon type=image/ico href=/favicon.ico>
    @vite('resources/css/front.css')

</head>
@php
$status = $__env->yieldContent('status', '404');
$bgPath = asset("images/{$status}.png");
@endphp
<body>
    <div class="absolute top-0 bottom-0 left-0 right-0 overflow-hidden bg-gray-400 py-24 sm:py-32" style="background-blend-mode: multiply;background-image: url('{{ $bgPath }}');">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-5xl font-semibold tracking-tight text-white sm:text-7xl">@yield('message')</h2>
                <p class="mt-8 text-lg font-medium text-pretty text-white sm:text-xl/8">@yield('description')</p>
            </div>
            <div class="mx-auto mt-10 max-w-2xl lg:mx-0 lg:max-w-none">
                <div class="grid grid-cols-1 gap-x-8 gap-y-6 text-base/7 font-semibold text-white sm:grid-cols-2 md:flex lg:gap-x-10">
                    <a href="/">トップページ <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </div>
    </div>
    @yield('content')
</body>

</html>
