const api = {
    preview: (url, $form) => axios.post(url, buildParam($form)),
};
const buildParam = function ($form) {
    const params = new URLSearchParams();
    const elements = $form.find('input, textarea, select').toArray();

    elements.forEach(el => {
        if (el.type === 'hidden' || el.type === 'text' || el.type === 'textarea') {
            return params.append(el.name, el.value)
        }
        if (el.type === 'checkbox') {
            return el.checked && params.append(el.name, el.value);
        }
        if (el.type === 'select-one') {
            return Number.isInteger(el.selectedIndex) && params.append(el.name, el.options[el.selectedIndex].value);
        }
    });
    return params;
}

const $form = $('.js-previewable-form');
const openPreview = async function (e) {
    e.preventDefault();
    const action = $form.data('preview-action');
    const res = await api.preview(action, $form).catch(err => {
        // バリデーションエラー
        if (err.response.status === 422) {
            const message = Object.values(err.response.data.errors).flat().join("\n");
            return alert(`エラーがあるためプレビューできません\n\n${message}`);
        }
        return alert('Error');
    })
    if (res && res.status === 200) {
        return renderPreviewWindow(res.data);
    }
}
$('.js-open-preview').on('click', openPreview);

let preview_window = null;
const renderPreviewWindow = function (html) {
    if (!preview_window || preview_window.closed) {
        preview_window = window.open();
    }
    preview_window.document.body.innerHTML = html;
}
