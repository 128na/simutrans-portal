<div class="modal fade" id="uploaderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('File Manager')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-right">
                <input type="file" class="d-none" name="upload_file" id="upload_file">
                <button type="button" class="btn btn-secondary js-open-upload-dialog">@lang('Upload File')</button>
                <button type="button" class="btn btn-primary js-select-file" disabled>@lang('Select File')</button>
            </div>
            <div class="modal-footer">
                <div class="hidable js-loading show">@lang('Loading...')</div>
                <div class="hidable js-uploading">@lang('Uploading...')</div>
                <div class="hidable js-file-list-box">
                    <ul class="js-file-list"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var url_default_image   = @json(asset('storage/'.config('attachment.no-thumbnail')));
    var msg_section_caption = @json(__('Caption'));
    var msg_section_text    = @json(__('Text'));
    var msg_section_image   = @json(__('Image'));
    var msg_section_url     = @json(__('Url'));
    var msg_remove_section  = @json(__('Remove'));
    var msg_open_uploader   = @json(__('Open File Manager'));
    var msg_no_file         = @json(__('No file.'));
    var msg_confirm         = @json(__('Are you sure you want to delete?'));
</script>
