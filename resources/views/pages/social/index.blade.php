@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('content')
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h1 mb-4">SNS・通知ツール</h2>
        <p class="v2-page-text-sub">記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。</p>
    </div>
    <div class="v2-page-content-area-lg">
        <div>
            <h4 class="v2-text-h3 mb-4">プッシュ通知</h4>
            <div class="text-c-sub">
                スマホやPCにプッシュ通知ができます。右下のアイコンから登録・解除ができます。
            </div>
        </div>

        <div>
            <h4 class="v2-text-h3 mb-4">SNSアカウント</h4>
            <div class="v2-table-wrapper">
                <table class="v2-table">
                    <tbody>
                        <tr>
                            <th>Twitter</th>
                            <td>
                                @include('components.ui.link', [
                                'url' => 'https://twitter.com/PortalSimutrans', 'title' => '@PortalSimutrans'])
                            </td>
                        </tr>
                        <tr>
                            <th>Misskey</th>
                            <td>
                                @include('components.ui.link', [
                                'url' => 'https://misskey.io/@PortalSimutrans', 'title' => '@PortalSimutrans'])
                            </td>
                        </tr>
                        <tr>
                            <th>Bluesky</th>
                            <td>
                                @include('components.ui.link', [
                                'url' => 'https://bsky.app/profile/portalsimutrans.bsky.social', 'title' => '@PortalSimutrans.bsky.social'])
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h4 class="v2-text-h3 mb-4">RSS</h4>
            <div class="v2-table-wrapper mb-4">
                <table class="v2-table">
                    <tbody>
                        @foreach(config('feed.feeds') as $feed)
                        <tr>
                            <th>{{ $feed['title'] }}</th>
                            <td>
                                <a href="{{ $feed['url'] }}" class="v2-link">
                                    <img src="{{ asset('storage/social/feed.png') }}" alt="RSS Feed" class="inline-block h-[1em] align-text-bottom mr-2">
                                    <span>
                                        {{ config('app.url') }}{{ $feed['url'] }}
                                    </span>
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
        </div>

        <div>
            <h4 class="v2-text-h3 mb-4">その他開発情報など</h4>
            <div class="v2-table-wrapper">
                <table class="v2-table">
                    <tbody>
                        <tr>
                            <th>中の人のTwitter</th>
                            <td>
                                @include('components.ui.link', [
                                'url' => 'https://twitter.com/128Na', 'title' => '@128Na'])
                            </td>
                        </tr>
                        <tr>
                            <th>Github</th>
                            <td>
                                @include('components.ui.link', [
                                'url' => 'https://github.com/128na/simutrans-portal', 'title' => '128na/simutrans-portal'])
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
