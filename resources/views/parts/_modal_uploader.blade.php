<div class="modal fade" id="uploaderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('message.uploader') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-right">
                <input type="file" class="d-none" name="upload_file" id="upload_file">
                <button type="button" class="btn btn-secondary js-open-upload-dialog">{{ __('message.upload-file') }}</button>
                <button type="button" class="btn btn-primary js-select-file" disabled>{{ __('message.select-file') }}</button>
            </div>
            <div class="modal-footer">
                <div class="hidable js-loading show">{{ __('message.loading') }}</div>
                <div class="hidable js-uploading">{{ __('message.uploading') }}</div>
                <div class="hidable js-file-list-box">
                    <ul class="js-file-list"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var url_default_image   = @json(asset('storage/'.config('attachment.no-thumbnail')));
    var msg_section_caption = @json(__('article.section-caption'));
    var msg_section_text    = @json(__('article.section-text'));
    var msg_section_image   = @json(__('article.section-image'));
    var msg_remove_section  = @json(__('article.remove-section'));
    var msg_open_uploader   = @json(__('message.open-uploader'));
    var msg_no_file         = @json(__('message.no-file'));
    var msg_confirm         = @json(__('message.confirm'));
</script>
