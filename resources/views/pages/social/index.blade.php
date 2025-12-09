@extends('layouts.front')

@section('max-w', '2-content-lg')
@section('content')
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">SNS・通知ツール</h2>
        <p class="v2-page-text-sub">記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。</p>
    </div>
    <div class="pt-6 v2-page-content-area">
        <h4 class="v2-text-h3">プッシュ通知</h4>
        <div class="text-c-sub">
            スマホやPCにプッシュ通知ができます。右下のアイコンから登録・解除ができます。
        </div>


        <h4 class="v2-text-h3">SNSアカウント</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">Twitter</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', [
                            'url' => 'https://twitter.com/PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">Misskey</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', [
                            'url' => 'https://misskey.io/@PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">Bluesky</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', [
                            'url' => 'https://bsky.app/profile/portalsimutrans.bsky.social', 'title' => '@PortalSimutrans.bsky.social'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4 class="v2-text-h3">RSS</h4>
        <div class="overflow-x-auto mb-4">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    @foreach(config('feed.feeds') as $feed)
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">{{ $feed['title'] }}</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            <a href="{{ $feed['url'] }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/social/feed.png') }}" alt="RSS Feed" class="inline-block h-[1em] align-text-bottom">
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-c-sub">
            RSSフィーダーやSlackなど各種ツールと連携させると新着情報が入手できます。
        </div>
        <h4 class="v2-text-h3">その他開発情報など</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">中の人のTwitter</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', [
                            'url' => 'https://twitter.com/128Na', 'title' => '@128Na'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">Github</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', [
                            'url' => 'https://github.com/128na/simutrans-portal', 'title' => '128na/simutrans-portal'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
