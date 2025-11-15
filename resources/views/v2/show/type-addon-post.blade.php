@php
$contents = $article->contents;
$fileInfo = $article->file?->fileInfo;
@endphp
<pre class="whitespace-pre-wrap text-gray-800">{{$contents->description}}</pre>

<h3 class="text-2xl font-semibold text-brand sm:text-2xl my-8">詳細情報</h3>
<div class="overflow-x-auto">
    <table class="border-collapse whitespace-nowrap">
        <tbody>
            <tr>
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">作者</td>
                <td class="border border-gray-300 px-4 py-2">{{ $contents->author ?? $article->user->name }}</td>
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
                <td class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">ダウンロード</td>
                <td class="border border-gray-300 px-4 py-2">
                    @include('v2.parts.link', [
                    'url' => route('articles.download', ['article' => $article->id]), 'title' => $article->file->original_name ?? 'download'])
                </td>
            </tr>
        </tbody>
    </table>
</div>


@if(($fileInfo->data['dats'] ?? false) || ($fileInfo->data['tabs'] ?? false))
<h4 class="text-xl font-semibold sm:text-xl my-8">ファイル情報</h4>

@if($fileInfo->data['dats']?? false)
<button type="button" command="--toggle" commandfor="dat-file" class="text-xl sm:text-xl my-4 p-4 flex w-full items-center justify-between bg-gray-100 cursor-pointer">
    Datファイル
    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
    </svg>
</button>

<el-disclosure id="dat-file" hidden class="mt-2 block space-y-2">
    <ul class="list-none">
        @foreach($fileInfo->data['dats'] as $filename => $addonNames)
        <li class="mb-1 break-all">{{ $filename }}</li>
        <li class="mb-6">
            <ul class="list-disc text-gray-400 ml-8 break-all">
                @foreach($addonNames as $addonName)
                <li><span class="text-black break-all">{{ $addonName }}</span></li>
                @endforeach
            </ul>
        </li>
        @endforeach
    </ul>
</el-disclosure>
@endif

@if($fileInfo->data['tabs']?? false)
<button type="button" command="--toggle" commandfor="tab-file" class="text-xl sm:text-xl my-4 p-4 flex w-full items-center justify-between bg-gray-100 cursor-pointer">
    Tabファイル
    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
    </svg>
</button>
<el-disclosure id="tab-file" hidden class="mt-2 block space-y-2">
    <ul class="list-none">
        @foreach($fileInfo->data['tabs'] as $filename => $translateMap)
        <li class="mb-1 break-all">{{ $filename }}</li>
        <li class="mb-6">
            <div class="overflow-x-auto">
                <table class="border-collapse whitespace-nowrap">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">アドオン名</th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-500 text-white">翻訳テキスト</th>
                        </tr>
                    <tbody>
                        @foreach($translateMap as $addonName => $translateName)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{$addonName}}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $translateName }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </li>
        @endforeach
    </ul>
</el-disclosure>
@endif
@endif

@if($contents->thanks)
<h4 class="text-xl font-semibold sm:text-xl my-8">謝辞</h4>
<pre class="whitespace-pre-wrap text-gray-800">{{$contents->thanks}}</pre>
@endif

@if($contents->license)
<h4 class="text-xl font-semibold sm:text-xl my-8">参考にしたアドオン</h4>
<pre class="whitespace-pre-wrap text-gray-800">{{$contents->license}}</pre>
@endif
