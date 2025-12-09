@extends('layouts.front')

@section('max-w', '2-content-lg')
@section('content')
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.google_recaptcha.siteKey') }}">
</script>

<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">
            Discord招待リンクの発行
        </h2>
        <p class="v2-page-text-sub">
            「シムトランス交流会議-~simutrans-interact-meeting~」の招待リンクを発行できます。<br>
            ※リンクは5分間のみ有効で、招待できる人数は1人までです。
        </p>
    </div>

    <div class="pt-6 v2-page-content-area">
        @if(isset($url))
        @include('components.ui.link', ['url' => $url])
        @else
        <form id="inviteForm" method="POST" action="{{ route('discord.generate') }}">
            @csrf
            <input type="hidden" id="recaptchaToken" name="recaptchaToken" value="" />
            <button type="submit" id="inviteDiscord" class="rounded-md bg-c-primary px-4 py-2 text-white cursor-pointer">
                発行する
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
