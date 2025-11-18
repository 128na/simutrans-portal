@php
$contents = $article->contents;
@endphp
<pre class="whitespace-pre-wrap text-gray-800 break-all">{{$contents->description}}</pre>

<h3 class="text-2xl font-semibold text-brand sm:text-2xl my-8">詳細情報</h3>
<div class="overflow-x-auto">
    <table class="border-collapse whitespace-nowrap">
        <tbody>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">作者</td>
                <td class="border border-gray-300 px-4 py-2">{{ $contents->author ?? $article->user->name }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">作者による掲載許可</td>
                <td class="border border-gray-300 px-4 py-2">{{ $contents->agreement ? '取得済み' : '未取得' }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">公開日時</td>
                <td class="border border-gray-300 px-4 py-2">{{ $article->published_at->format('Y/m/d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">最終更新日時</td>
                <td class="border border-gray-300 px-4 py-2">{{ $article->modified_at->format('Y/m/d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">掲載URL</td>
                <td class="border border-gray-300 px-4 py-2">
                    @include('v2.parts.link-external', [
                    'url' => $contents->link, 'title' => $contents->link])
                </td>
            </tr>
        </tbody>
    </table>
</div>
