const $modal = $('#uploaderModal');

const $loading = $modal.find('.js-loading');
const $uploading = $modal.find('.js-uploading');
const $file_list = $modal.find('.js-file-list');
const $file_list_box = $modal.find('.js-file-list-box');
const $select_button = $modal.find('.js-select-file');
const $upload_file = $modal.find('#upload_file');

let only_image = false;
let selected_file_id = null;
let file_list = [];
let $apply_to_input = null;
let $apply_to_preview = null;
let $apply_to_preview_url = null;

const api = {
    fetchAttachments: () => axios.get(`/api/v1/attachments/my`),
    fetchImageAttachments: () => axios.get(`/api/v1/attachments/myimage`),
    uploadAttachment: params => axios.post(`/api/v1/attachments/upload`, params, {
        headers: { 'content-type': 'multipart/form-data' }
    }),
    deleteAttachment: id => axios.delete(`/api/v1/attachments/${id}`),
};

// ファイル一覧取得
const syncFileList = async function () {
    try {
        const res = only_image
            ? await api.fetchImageAttachments()
            : await api.fetchAttachments();

        if (res.status === 200) {
            file_list = res.data.data.reverse();
            renderAll();
            showFileList();
        } else {
            showErrorAndCloseModal(res);
        }
    } catch (e) {
        showErrorAndCloseModal(e.response);
    }
}

// 表示処理
const renderAll = function () {
    renderFileList();
    renderSelectButton();
}
const renderFileList = function (list) {
    const html = (list || file_list).map(item => {
        return `
            <li class="mb-2 ${item.id == selected_file_id ? 'selected' : ''}">
                <img class="img-thumbnail js-selectable" src="${item.thumbnail}" data-id="${item.id}">
                <div class="js-selectable" data-id="${item.id}">${item.original_name}</div>
                <button type="button" class="close js-delete-file" data-id="${item.id}"><span>&times;</span></button>
            </li>`;
    }).join('');
    return $file_list.html(html || msg_no_file);
}
const renderSelectButton = function () {
    $select_button.prop('disabled', !selected_file_id);
}

// アイテム選択
const itemSelect = function (e) {
    const $target = $(e.currentTarget);
    toggleSelectedFileId($target.data('id'));
    renderAll();
}
const toggleSelectedFileId = function (id) {
    selected_file_id = selected_file_id == id ? null : id;
}
$modal.on('click', '.js-selectable', itemSelect)

const itemApply = function () {
    const item = file_list.find(item => item.id == selected_file_id);

    $apply_to_input.val(item.id);
    if ($apply_to_preview) {
        if ($apply_to_preview.prop("tagName") === 'IMG') {
            $apply_to_preview.attr('src', item.thumbnail);
            $apply_to_preview_url.val(item.thumbnail);
        } else {
            $apply_to_preview.text(item.original_name);
        }
    }
    $modal.modal('hide');
};
$select_button.on('click', itemApply);

// アップローダー表示
const openUploader = function (e) {
    e.preventDefault();
    showLoading();

    only_image = false;
    selected_file_id = null;
    file_list = [];
    $apply_to_input = null;
    $apply_to_preview = null;
    $apply_to_preview_url = null;

    const $target = $(e.currentTarget);
    $apply_to_input = $($target.data('input'));
    if ($target.data('preview')) {
        $apply_to_preview = $($target.data('preview'));
        $apply_to_preview_url = $($target.data('preview-url'));
    }
    toggleSelectedFileId($apply_to_input.val());

    only_image = $target.data('only-image');
    $modal.modal('show');
    syncFileList();
}
$('form').on('click', '.js-open-uploader', openUploader)

// ファイルアップロードダイアログ表示
const openUploadDialog = function () {
    $upload_file.click();
    return false;
};
$('.js-open-upload-dialog').on('click', openUploadDialog);

// ファイルアップロード
const uploadFile = async function (e) {
    const file = $(e.currentTarget).prop('files')[0];
    const formData = new FormData();
    formData.append('file', file);

    showUploading();
    try {
        const res = await api.uploadAttachment(formData);
        if (res.status === 200) {
            syncFileList();
        } else {
            showErrorAndCloseModal(res);
        }
    } catch (e) {
        showErrorAndCloseModal(e.response);
    }
}
$upload_file.on('change, input', uploadFile);

const showLoading = function () {
    $loading.addClass('show');
    $uploading.removeClass('show');
    $file_list_box.removeClass('show');
}
const showUploading = function () {
    $loading.removeClass('show');
    $uploading.addClass('show');
    $file_list_box.removeClass('show');
}
const showFileList = function () {
    $loading.removeClass('show');
    $uploading.removeClass('show');
    $file_list_box.addClass('show');
}

const deleteFile = async function (e) {
    e.preventDefault();
    if (!confirm(msg_confirm)) {
        return;
    }

    const id = $(e.currentTarget).data('id');
    try {
        const res = await api.deleteAttachment(id);
        if (res.status === 200) {
            syncFileList();
        } else {
            showErrorAndCloseModal(res);
        }
    } catch (e) {
        showErrorAndCloseModal(e.response);
    }
}
$modal.on('click', '.js-delete-file', deleteFile);

const showErrorAndCloseModal = function (res) {
    console.warn(res);
    alert('Error');
    $modal.modal('hide');
}
