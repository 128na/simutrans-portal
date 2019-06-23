

<div class="form-group">
    <label><span class="badge badge-danger mr-1">{{ __('message.required') }}</span>{{ __('article.sections') }}</label>
    <div id="sections" class="mb-1">
        @forelse (old('sections', isset($article) ? $article->getContents('sections', []) : []) as $index => $section)
            <div class="form-group js-section" data-section="{{ $index }}">
                @switch($section['type'])
                    @case('caption')
                        <h5 class="row">
                            <div class="col-8">{{ __('article.section-caption') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('article.remove-section') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="caption">
                        <input type="text" class="form-control" name="sections[{{ $index }}][caption]" value="{{ $section['caption'] }}">
                        @break
                    @case('text')
                        <h5 class="row">
                            <div class="col-8">{{ __('article.section-text') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('article.remove-section') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="text">
                        <textarea class="form-control" name="sections[{{ $index }}][text]" rows="8">{!! e($section['text']) !!}</textarea>
                        @break
                    @case('image')
                        <h5 class="row">
                            <div class="col-8">{{ __('article.section-image') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('article.remove-section') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="image">
                        <div class="mb-2">
                            <img id="thumbnail-preview_{{ $index }}" class="preview img-thumbnail " src="{{ isset($section['id']) ? $article->getImageUrl($section['id']) : asset('storage/'.config('attachment.no-thumbnail')) }}">
                        </div>
                        <div class="custom-file">
                            <label class="custom-file-label" for="thumbnail">{{ old('thumbnail', isset($article) ? $article->getImage($section['id'])->original_name : __('message.not-selected')) }}</label>
                            <input type="file" class="custom-file-input js-preview-trigger" name="sections[{{ $index }}][image]" data-preview="#thumbnail-preview_{{ $index }}">
                            <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section['id'] ?? '' }}">
                        </div>
                        @break
                @endswitch
                <hr>
            </div>
        @empty
            <div class="form-group js-section" data-section="0">
                <h5 class="row">
                    <div class="col-8">{{ __('article.section-text') }}</div>
                    <div class="col-4 ml-auto text-right">
                        <button class="btn btn-sm btn-danger js-remove-section">{{ __('article.remove-section') }}</button>
                    </div>
                </h5>
                <input type="hidden" name="sections[0][type]" value="text">
                <textarea class="form-control" name="sections[0][text]" rows="8"></textarea>
            </div>
        @endforelse
    </div>
    <a href="#" class="btn btn-secondary js-add-caption-section">{{ __('article.add-caption-section') }}</a>
    <a href="#" class="btn btn-secondary js-add-text-section">{{ __('article.add-text-section') }}</a>
    <a href="#" class="btn btn-secondary js-add-image-section">{{ __('article.add-image-section') }}</a>
</div>
<script>
    var url_default_image = @json(asset('storage/'.config('attachment.no-thumbnail')));
    var msg_section_caption = @json(__('article.section-caption'));
    var msg_section_text = @json(__('article.section-text'));
    var msg_section_image = @json(__('article.section-image'));
    var msg_remove_section = @json(__('article.remove-section'));
</script>
