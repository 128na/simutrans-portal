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

// タグ追加
const $tag_area = $('.tag-list');
const $new_tag = $('#new-tag');
const addTag = function () {
    const names = $new_tag.val()
        .replace(/[、　,\s]/i, ',')
        .split(',')
        .map(s => s.trim())
        .filter(s => s);

    const html = names.reduce((html, name) => html + buildTagHTML(name), '');
    $tag_area.append(html);
    $new_tag.val('');
};
$('.js-add-tag').on('click ', addTag);
const addTagIfEnter = function (e) {
    // Enterの時はSubmitせずタグを追加する
    if (e.keyCode === 13) {
        e.preventDefault();
        addTag();
    }
};
$new_tag.on('keydown', addTagIfEnter);
const buildTagHTML = function (name) {
    return `
        <div class="badge badge-secondary">
            <span class="mr-1">${name}</span>
            <span class="badge badge-pill badge-light clickable js-remove-tag">&times;</span>
            <input type="hidden" name="tags[]" value="${name}">
        </div>`;
};

// タグ削除
const removeTag = function (e) {
    $(e.currentTarget).parent('div').remove();
};
$('.tag-list').on('click', '.js-remove-tag', removeTag);

// 人気のタグを追加
const addPopularTag = function (e) {
    e.preventDefault();
    const name = $(e.currentTarget).data('name');
    $tag_area.append(buildTagHTML(name));
};
$('.js-add-popular-tag').on('click ', addPopularTag);
