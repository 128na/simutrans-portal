@php
$title = str_replace('https://', '',$website);
// 表示名をいい感じにする
switch (true) {
// SNS
case str_starts_with($website, 'https://twitter.com/') || str_starts_with($website, 'https://x.com/'):
$title = 'Twitter';
break;
case str_starts_with($website, 'https://misskey.io/'):
$title = 'Misskey';
break;
case str_starts_with($website, 'https://bsky.app/'):
$title = 'Bluesky';
break;
// 動画サイト
case str_starts_with($website, 'https://www.youtube.com/'):
$title = 'YouTube';
break;
case str_starts_with($website, 'https://www.nicovideo.jp/'):
$title = 'Niconico';
break;
// その他有名サイト
case str_starts_with($website, 'https://github.com/'):
$title = 'GitHub';
break;
}
@endphp
@include('v2.parts.link-external', ['url' => $website, 'title' => $title])
