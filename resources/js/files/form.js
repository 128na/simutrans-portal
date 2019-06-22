// カスタムファイル選択
const bsCustomFileInput = require('bs-custom-file-input');
bsCustomFileInput.init()

// アップロード画像プレビュー
$('form').on('change', '.js-preview-trigger', function (e) {
    const $target = $(e.currentTarget);
    const selector = $target.data('preview');

    $(selector).attr('src', URL.createObjectURL(e.target.files[0]));
});

// 投稿形式選択切替
const $input_post = $('#js-post');
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    const $target = $(e.target);
    $input_post.val($target.attr('aria-controls'));
})
