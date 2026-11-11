{{-- 新着カード。一覧ページと同じカード（ArticleCard.tsx）・列数で React が描画する（resources/js/front/pages/LatestCardsPage.tsx） --}}
<script id="data-latest-cards-{{ $key }}" type="application/json">
  @json($cards)
</script>
<div data-latest-cards="data-latest-cards-{{ $key }}">読み込み中...</div>
