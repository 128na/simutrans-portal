@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.google_recaptcha.siteKey') }}">
</script>

<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl">
            Discord招待リンクの発行
        </h2>
        <p class="mt-2 text-lg/8 text-gray-600">
            「シムトランス交流会議-~simutrans-interact-meeting~」の招待リンクを発行できます。<br>
            ※リンクは5分間のみ有効で、招待できる人数は1人までです。
        </p>
    </div>

    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        @if(isset($url))
        @include('components.ui.link-external', ['url' => $url])
        @else
        <form id="inviteForm" method="POST" action="{{ route('discord.generate') }}">
            @csrf
            <input type="hidden" id="recaptchaToken" name="recaptchaToken" value="" />
            <button type="submit" id="inviteDiscord" class="rounded-md bg-brand px-4 py-2 text-white cursor-pointer">
                発行する
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
