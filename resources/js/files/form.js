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

// セクション追加
const $sections = $('#sections');
const add_caption_section = function (e) {
    e.preventDefault();
    const current_index = $('.js-section').last().data('section');
    $sections.append(buildCaptionSectionHTML(current_index + 1));
};
$('.js-add-caption-section').on('click', add_caption_section);

const add_text_section = function (e) {
    e.preventDefault();
    const current_index = $('.js-section').last().data('section');
    $sections.append(buildTextSectionHTML(current_index + 1));
};
$('.js-add-text-section').on('click', add_text_section);

const add_image_section = function (e) {
    e.preventDefault();
    const current_index = $('.js-section').last().data('section');
    $sections.append(buildImageSectionHTML(current_index + 1));
};
$('.js-add-image-section').on('click', add_image_section);

const buildCaptionSectionHTML = function (index) {
    return `
        <div class="form-group js-section" data-section="${index}">
            <h5 class="row">
                <div class="col-8">${msg_section_caption}</div>
                <div class="col-4 ml-auto text-right">
                    <button class="btn btn-sm btn-danger js-remove-section">${msg_remove_section}</button>
                </div>
            </h5>
            <input type="hidden" name="sections[${index}][type]" value="caption">
            <input type="text" class="form-control" name="sections[${index}][caption]" value="">
        </div>`;
};
const buildTextSectionHTML = function (index) {
    return `
        <div class="form-group js-section" data-section="${index}">
            <h5 class="row">
                <div class="col-8">${msg_section_text}</div>
                <div class="col-4 ml-auto text-right">
                    <button class="btn btn-sm btn-danger js-remove-section">${msg_remove_section}</button>
                </div>
            </h5>
            <input type="hidden" name="sections[${index}][type]" value="text">
            <textarea class="form-control" name="sections[${index}][text]" rows="8"></textarea>
        </div>`;
};
const buildImageSectionHTML = function (index) {
    return `
        <div class="form-group js-section" data-section="${index}">
            <h5 class="row">
                <div class="col-8">${msg_section_image}</div>
                <div class="col-4 ml-auto text-right">
                    <button class="btn btn-sm btn-danger js-remove-section">${msg_remove_section}</button>
                </div>
            </h5>
            <input type="hidden" name="sections[${index}][type]" value="image">
            <div class="mb-2">
                <img id="thumbnail-preview_${index}" class="preview img-thumbnail " src="${url_default_image}">
            </div>
            <div class="custom-file">
                <label class="custom-file-label" for="thumbnail"></label>
                <input type="file" class="custom-file-input js-preview-trigger" name="sections[${index}][image]" data-preview="#thumbnail-preview_${index}">
            </div>
        </div>`;
};

// セクション削除
const remove_section = function (e) {
    e.preventDefault();
    $(e.currentTarget).parents('.js-section').remove();
};
$sections.on('click', '.js-remove-section', remove_section);
