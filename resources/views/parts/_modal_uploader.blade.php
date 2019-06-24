<div class="modal fade" id="uploaderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('message.uploader') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="file" class="d-none" name="upload_file" id="upload_file">
                <button type="button" class="btn btn-secondary js-open-upload-dialog">{{ __('message.upload-file') }}</button>
                <button type="button" class="btn btn-primary js-select-file" disabled>{{ __('message.select-file') }}</button>
            </div>
            <div class="modal-footer">
                <div class="hidable js-loading show">{{ __('message.loading') }}</div>
                <div class="hidable js-uploading">{{ __('message.uploading') }}</div>
                <ul class="hidable js-file-list">
                </ul>
            </div>
        </div>
    </div>
</div>
