@extends('layouts.front')

@section('max-w', 'v2-page-lg')
@section('page-content')
  <div class="v2-page v2-page-lg">
    <div class="mb-12">
      <h2 class="v2-text-h1 mb-4">サイトを応援する</h2>
      <p class="v2-page-text-sub">
        サーバー費用等の運営費を支援いただけます。支援は完全に任意で、支援の有無によって閲覧・投稿などのサイト機能は変わりません。
      </p>
    </div>
    <div class="v2-page-content-area-lg">
      <div>
        <h4 class="v2-text-h3 mb-4">国内の方向け（OFUSE）</h4>
        <div class="text-c-sub mb-4">
          OFUSEは日本円建ての投げ銭サービスです。単発でのご支援・毎月の継続支援のどちらにも対応しています。
        </div>
        <div class="v2-table-wrapper">
          <table class="v2-table">
            <tbody>
              <tr>
                <th>単発で支援する</th>
                <td>
                  <x-ui.link :url="'https://ofuse.me/128na/letter'" :title="'OFUSEで単発のお便り・支援を送る'" />
                </td>
              </tr>
              <tr>
                <th>毎月支援する</th>
                <td>
                  <x-ui.link :url="'https://ofuse.me/memberships/4980'" :title="'OFUSEで毎月の継続支援をする'" />
                </td>
              </tr>
              <tr>
                <th>OFUSEページ</th>
                <td>
                  <x-ui.link :url="'https://ofuse.me/128na'" :title="'OFUSE 128naのページ'" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="text-c-sub mt-4">
          ドル建てでの支援をご希望の場合は、下記のGitHub Sponsorsもご利用いただけます。
        </div>
      </div>

      <hr class="my-8" />

      <div>
        <h4 class="v2-text-h3 mb-4">For overseas supporters</h4>
        <div class="text-c-sub mb-4">
          If you'd like to support the running costs of this site (such as server fees), you're welcome to do so via GitHub Sponsors. Support is entirely optional and doesn't change any site features.
        </div>
        <div class="v2-table-wrapper">
          <table class="v2-table">
            <tbody>
              <tr>
                <th>GitHub Sponsors</th>
                <td>
                  <x-ui.link :url="'https://github.com/sponsors/128na'" :title="'Sponsor 128na on GitHub'" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="text-c-sub mt-4">
          JPY-denominated support is also available via OFUSE (see the Japanese section above).
        </div>
      </div>
    </div>
  </div>
@endsection
