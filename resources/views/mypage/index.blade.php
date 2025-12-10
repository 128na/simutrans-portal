@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">マイページ</h2>
    </div>
    <div class="v2-page-content-area-lg">
        <h3 class="v2-text-h3">ダッシュボード</h3>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">投稿記事</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->article_count ?? 0}} 件</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @include('components.ui.link', ['url' => route('mypage.articles.index'), 'title' => '記事一覧']),
                            @include('components.ui.link', ['url' => route('mypage.articles.create'), 'title' => '作成'])
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">記事リダイレクト設定数</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->redirect_count ?? 0}} 件</td>
                        <td class="border border-c-sub/10 px-4 py-2">@include('components.ui.link', ['url' => route('mypage.redirects'), 'title' => 'リダイレクト設定'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">投稿ファイル数</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->attachment_count ?? 0}} 件</td>
                        <td class="border border-c-sub/10 px-4 py-2" rowspan="2">@include('components.ui.link', ['url' => route('mypage.attachments'), 'title' => 'ファイル管理'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ファイルストレージ使用量</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{ round(($summary->total_attachment_size ?? 0)/1024/1024,1) }} MB</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">今月の合計PV数</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->total_view_count ?? 0}} 件</td>
                        <td class="border border-c-sub/10 px-4 py-2" rowspan="2">@include('components.ui.link', ['url' => route('mypage.analytics'), 'title' => 'アナリティクス'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">今月の合計CV数</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->total_conversion_count ?? 0}} 件</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">作成したタグ</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$summary->tag_count ?? 0}} 件</td>
                        <td class="border border-c-sub/10 px-4 py-2">@include('components.ui.link', ['url' => route('mypage.tags'), 'title' => 'タグ編集'])</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3 class="v2-text-h3">ユーザー情報</h3>
        <div class="overflow-x-auto">
            <table class="border-collapse whitespace-nowrap">
                <tbody>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ユーザー名</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->name}}</td>
                        <td class="border border-c-sub/10 px-4 py-2" rowspan="2">@include('components.ui.link', ['url' => route('mypage.profile'), 'title' => 'プロフィール編集'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ニックネーム</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->nickname ?? '未設定'}}</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">ユーザータイプ</td>
                        <td class="border border-c-sub/10 px-4 py-2">@lang("role.{$user->role->value}")</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @if($user->role === \App\Enums\UserRole::Admin)
                            @include('components.ui.link', ['url' => route('admin.index'), 'title' => '管理画面'])
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">メールアドレス認証</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->email_verified_at ? '✅完了' : '⚠️未完了'}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">
                            @if($user->email_verified_at)
                            -
                            @else
                            @include('components.ui.link', ['url' => route('mypage.verify-email'), 'title' => '認証'])
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">二要素認証</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->two_factor_confirmed_at ? '✅有効' : '⚠️無効'}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">@include('components.ui.link', ['url' => route('mypage.two-factor'), 'title' => '設定'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">招待リンク</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->invitation_code ? '⚠️発行済み' : '✅未発行'}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">@include('components.ui.link', ['url' => route('mypage.invite'), 'title' => '設定'])</td>
                    </tr>
                    <tr>
                        <td class="border border-c-sub/10 px-4 py-2 bg-c-sub/80 text-white">最終ログイン日時</td>
                        <td class="border border-c-sub/10 px-4 py-2">{{$user->loginHistories()->latest()->first()?->created_at->format('Y/m/d H:i:s')}}</td>
                        <td class="border border-c-sub/10 px-4 py-2">@include('components.ui.link', ['url' => route('mypage.login-histories'), 'title' => 'ログイン履歴'])</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
