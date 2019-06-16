// カスタムファイル選択
const bsCustomFileInput = require('bs-custom-file-input');
bsCustomFileInput.init()

// アップロード画像プレビュー
$('form').on('change', '.js-preview-trigger', function (e) {
    const $target = $(e.currentTarget);
    const selector = $target.data('preview');

    $(selector).attr('src', URL.createObjectURL(e.target.files[0]));
});
