@extends('layouts.front')

@section('max-w', 'max-w-7xl')
@section('content')
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div class="mb-6">
        <h2 class="title-xl2">SNS・通知ツール</h2>
        <p class="mt-2 text-lg/8 text-secondary">記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。</p>
    </div>
    <div class="flex flex-col gap-y-4 border-t border-gray-200 pt-6 lg:mx-0">
        <h4 class="title-md">プッシュ通知</h4>
        <div class="text-secondary">
            スマホやPCにプッシュ通知ができます。右下のアイコンから登録・解除ができます。
        </div>


        <h4 class="title-md">SNSアカウント</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Twitter</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('components.ui.link-external', [
                            'url' => 'https://twitter.com/PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Misskey</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('components.ui.link-external', [
                            'url' => 'https://misskey.io/@PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Bluesky</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('components.ui.link-external', [
                            'url' => 'https://bsky.app/profile/portalsimutrans.bsky.social', 'title' => '@PortalSimutrans.bsky.social'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4 class="title-md">RSS</h4>
        <div class="overflow-x-auto mb-4">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    @foreach(config('feed.feeds') as $feed)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">{{ $feed['title'] }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <a href="{{ $feed['url'] }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/social/feed.png') }}" alt="RSS Feed" class="inline-block h-[1em] align-text-bottom">
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-secondary">
            RSSフィーダーやSlackなど各種ツールと連携させると新着情報が入手できます。
        </div>
        <h4 class="title-md">その他開発情報など</h4>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">中の人のTwitter</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('components.ui.link-external', [
                            'url' => 'https://twitter.com/128Na', 'title' => '@128Na'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Github</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('components.ui.link-external', [
                            'url' => 'https://github.com/128na/simutrans-portal', 'title' => '128na/simutrans-portal'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
