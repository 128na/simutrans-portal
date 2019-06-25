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
            return el.selectedIndex && params.append(el.name, el.options[el.selectedIndex].value);
        }
        // console.warn('non support type:' + el.type);
    });
    return params;
}

const $form = $('.js-previewable-form');
const openPreview = async function (e) {
    e.preventDefault();
    const action = $form.data('preview-action');
    api.preview(action, $form).catch(err => {
        if (err.response.status === 418) {
            return renderPreviewWindow(err.response.data);
        }

        // バリデーションエラー
        if (err.response.status === 422) {
            const message = Object.values(err.response.data.errors).flat().join("\n");
            return alert(`エラーがあるためプレビューできません\n\n${message}`);
        }
        return alert('Error');
    })
}
$('.js-open-preview').on('click', openPreview);


let preview_window = null;
const renderPreviewWindow = function (html) {
    if (!preview_window || preview_window.closed) {
        preview_window = window.open();
    }
    preview_window.document.body.innerHTML = html;
}
