@switch($pak)
  @case('128-japan')
    @include('components.ui.link', ['url' => 'https://forum.simutrans.com/index.php?board=119.0', 'title' => 'Pak128.Japan'])
    は日本の車両を中心に揃えた
    @include('components.ui.link', ['url' => 'http://forum.simutrans.com/index.php?board=26.0', 'title' => 'Pak128'])
    の派生版です。
    <br />
    Pak128と車両のスケールが異なるほか、産業の種類も異なるため互換性のないアドオンもあります。

    @break
  @case('128')
    公式の
    @include('components.ui.link', ['url' => 'http://forum.simutrans.com/index.php?board=26.0', 'title' => 'Pak128'])
    はpak64よりも大きなグラフィックが特徴です。
    <br />

    @break
  @case('64')
    公式の
    @include('components.ui.link', ['url' => 'https://forum.simutrans.com/index.php?board=42.0', 'title' => 'Pak64'])
    はもっとも古くから存在します。
    <br />
    @include('components.ui.link', ['url' => 'https://github.com/wa-st/pak-nippon/releases', 'title' => 'Pak.nippon'])
    など各種派生版のアドオンも含まれています。

    @break
  @case('other-pak')
    Pak64, 128, 128.Japan以外のpakセット向けアドオンの一覧です。
    <br />
    設定ファイルやメニューバーなど、pakセットに関係なく使えるコンテンツも含まれます。

    @break
@endswitch
