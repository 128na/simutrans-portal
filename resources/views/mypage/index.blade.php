@extends('layouts.mypage')
@section('max-w', 'v2-page-lg')
@section('page-content')
<div class="v2-page v2-page-lg">
    <div class="mb-12">
        <h2 class="v2-text-h2">マイページ</h2>
    </div>
    <div class="v2-page-content-area-lg">
        <div>
            <h3 class="v2-text-h3 mb-4">サマリ</h3>
            <div class="v2-table-wrapper">
                <table class="v2-table">
                    <tbody>
                        <tr>
                            <th>投稿数</th>
                            <td>{{$summary->article_count ?? 0}} 件</td>
                            <td>
                                @include('components.ui.link', ['url' => route('mypage.articles.index'), 'title' => '記事一覧']),
                                @include('components.ui.link', ['url' => route('mypage.articles.create'), 'title' => '作成'])
                            </td>
                        </tr>
                        <tr>
                            <th>記事リダイレクト設定数</th>
                            <td>{{$summary->redirect_count ?? 0}} 件</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.redirects'), 'title' => 'リダイレクト設定'])</td>
                        </tr>
                        <tr>
                            <th>作成マイリスト数（公開数）</th>
                            <td>{{$summary->mylist_count ?? 0}} 件（{{$summary->public_mylist_count ?? 0}} 件）</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.mylists.index'), 'title' => 'マイリスト管理'])</td>
                        </tr>
                        <tr>
                            <th>投稿ファイル数</th>
                            <td>{{$summary->attachment_count ?? 0}} 件</td>
                            <td rowspan="2">@include('components.ui.link', ['url' => route('mypage.attachments'), 'title' => 'ファイル管理'])</td>
                        </tr>
                        <tr>
                            <th>ファイルストレージ使用量</th>
                            <td>{{ round(($summary->total_attachment_size ?? 0)/1024/1024,1) }} MB</td>
                        </tr>
                        <tr>
                            <th>今月の合計PV数</th>
                            <td>{{$summary->total_view_count ?? 0}} 件</td>
                            <td rowspan="2">@include('components.ui.link', ['url' => route('mypage.analytics'), 'title' => 'アナリティクス'])</td>
                        </tr>
                        <tr>
                            <th>今月の合計CV数</th>
                            <td>{{$summary->total_conversion_count ?? 0}} 件</td>
                        </tr>
                        <tr>
                            <th>作成したタグ数</th>
                            <td>{{$summary->tag_count ?? 0}} 件</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.tags'), 'title' => 'タグ編集'])</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <h3 class="v2-text-h3 mb-4">ユーザー情報</h3>
            <div class="v2-table-wrapper">
                <table class="v2-table">
                    <tbody>
                        <tr>
                            <th>ユーザー名</th>
                            <td>{{$user->name}}</td>
                            <td rowspan="2">@include('components.ui.link', ['url' => route('mypage.profile'), 'title' => 'プロフィール編集'])</td>
                        </tr>
                        <tr>
                            <th>ニックネーム</th>
                            <td>{{$user->nickname ?? '未設定'}}</td>
                        </tr>
                        <tr>
                            <th>ユーザータイプ</th>
                            <td>@lang("role.{$user->role->value}")</td>
                            <td>
                                @if($user->role === \App\Enums\UserRole::Admin)
                                @include('components.ui.link', ['url' => route('admin.index'), 'title' => '管理画面'])
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>メールアドレス認証</th>
                            <td>{{$user->email_verified_at ? '✅完了' : '⚠️未完了'}}</td>
                            <td>
                                @if($user->email_verified_at)
                                -
                                @else
                                @include('components.ui.link', ['url' => route('mypage.verify-email'), 'title' => '認証'])
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>二要素認証</th>
                            <td>{{$user->two_factor_confirmed_at ? '✅有効' : '⚠️無効'}}</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.two-factor'), 'title' => '設定'])</td>
                        </tr>
                        <tr>
                            <th>招待リンク</th>
                            <td>{{$user->invitation_code ? '⚠️発行済み' : '✅未発行'}}</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.invite'), 'title' => '設定'])</td>
                        </tr>
                        <tr>
                            <th>最終ログイン日時</th>
                            <td>{{$user->loginHistories()->latest()->first()?->created_at->format('Y/m/d H:i:s')}}</td>
                            <td>@include('components.ui.link', ['url' => route('mypage.login-histories'), 'title' => 'ログイン履歴'])</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
