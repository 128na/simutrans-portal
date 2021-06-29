@extends('layouts.front')

@section('title', '新規登録申請フォーム')

@section('content')


    <h2>新規登録申請フォーム</h2>

    <ol>
        <li>このフォームから登録申請を送信して下さい</li>
        <li>
            中の人が気づいたときにメールアドレスまたはTwitterアカウント宛に登録情報の確認を行います。<br>
            メールの場合はsimutrans.128na@gmail.com、Twitterの場合は@128NaよりDMにて連絡します。
        </li>
        <li>確認が完了すると登録時のメールアドレスに「パスワードリセットがリクエストされました」メールが届きます。</li>
        <li>メール内の指示に従ってパスワードを設定するとログイン可能になります。</li>
    </ol>
    <form method="POST" action="{{ route('registrationOrders.store') }}">
        @csrf
        <div class="form-group">
            <label for="name"><span class="badge badge-danger">必須</span> ユーザー名</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label for="email"><span class="badge badge-danger">必須</span> メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
            <small class="form-text text-muted">ユーザー登録と登録時の確認に使用されます。</small>
        </div>
        <div class="form-group">
            <label for="twitter"><span class="badge badge-danger">必須</span> Twitterユーザー名</label>
            <input type="text" class="form-control" id="twitter" name="twitter" placeholder="@twitter"
                value="{{ old('twitter') }}">
            <small class="form-text text-muted">登録時の確認に使用されます。</small>
        </div>
        <div class="form-group">
            <label for="code"><span class="badge badge-secondary">任意</span> 招待コード</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}">
            <small class="form-text text-muted">コードをお持ちの人は入力してください。</small>
        </div>

        <button type="submit" class="btn btn-primary">送信</button>
    </form>

@endsection
