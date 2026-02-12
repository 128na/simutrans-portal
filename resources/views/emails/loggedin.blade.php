@include('emails.header', ['name' => $user->name])

<p>
  ご利用のアカウントで{{ $loginHistory->created_at->format('Y/m/d H:i') }}にログインがありました。
</p>
<p>== ログイン情報 ==</p>
<p>
  IPアドレス
  <br />
  {{ $loginHistory->ip ?? '不明' }}
  <br />
  アクセス元
  <br />
  {{ $loginHistory->referer ?? '不明' }}
  <br />
  ユーザーエージェント（ブラウザ情報）
  <br />
  {{ $loginHistory->ua ?? '不明' }}
  <br />
</p>
@include('emails.footer')
