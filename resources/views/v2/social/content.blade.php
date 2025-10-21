<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
            appId: "{{ config('onesignal.app_id') }}"
            , safari_web_id: "web.onesignal.auto.4bf12d4e-2e1c-4e2f-be7e-e4e315c9ca64"
            , notifyButton: {
                enable: true
            , }
            , allowLocalhostAsSecureOrigin: true
        , });
    });
    const handleOneSign = async () => {
        if (!window.OneSignalDeferred.isPushNotificationsSupported()) {
            window.alert('このデバイスはプッシュ通知未対応です');
            return;
        }

        // 既に通知許可済みならフラグ切替のみ
        if (await window.OneSignalDeferred.isPushNotificationsEnabled()) {
            window.alert('登録済みです。');
            return;
        }

        window.OneSignalDeferred.showNativePrompt();
    };

</script>

<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <div>
        <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl">SNS・通知ツール</h2>
        <p class="mt-2 text-lg/8 text-gray-600">記事投稿や更新通知を受け取ることができるSNSアカウントやツールです。</p>
    </div>
    <div class="mt-10 border-t border-gray-200 pt-10 sm:mt-8 sm:pt-8 lg:mx-0">
        <h3 class="text-xl font-semibold sm:text-xl my-8">プッシュ通知</h3>
        <div class="text-gray-600">
            スマホやPCにプッシュ通知ができます。右下のアイコンから登録・解除ができます。
        </div>


        <h3 class="text-xl font-semibold sm:text-xl my-8">SNSアカウント</h3>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Twitter</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link-external', [
                            'url' => 'https://twitter.com/PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Misskey</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link-external', [
                            'url' => 'https://misskey.io/@PortalSimutrans', 'title' => '@PortalSimutrans'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Bluesky</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link-external', [
                            'url' => 'https://bsky.app/profile/portalsimutrans.bsky.social', 'title' => '@PortalSimutrans.bsky.social'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3 class="text-xl font-semibold sm:text-xl my-8">RSS</h3>
        <div class="overflow-x-auto mb-4">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    @foreach(config('feed.feeds') as $feed)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">{{ $feed['title'] }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link', [
                            'url' => $feed['url'],
                            'title' => '🛜'
                            ])
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-gray-600">
            RSSフィーダーやSlackなど各種ツールと連携させると新着情報が入手できます。
        </div>
        <h3 class="text-xl font-semibold sm:text-xl my-8">その他開発情報など</h3>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">中の人のTwitter</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link-external', [
                            'url' => 'https://twitter.com/128Na', 'title' => '@128Na'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">Github</td>
                        <td class="border border-gray-300 px-4 py-2">
                            @include('v2.parts.link-external', [
                            'url' => 'https://github.com/128na/simutrans-portal', 'title' => '128na/simutrans-portal'])
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
