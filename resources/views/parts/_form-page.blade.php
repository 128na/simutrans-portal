

<div class="form-group">
    <label><span class="badge badge-danger mr-1">{{ __('Required') }}</span>{{ __('Sections') }}</label>
    <div id="sections" class="mb-1">
        @forelse (old('sections', isset($article) ? $article->getContents('sections', []) : []) as $index => $section)
            <div class="form-group js-section" data-section="{{ $index }}">
                @switch($section['type'])
                    @case('caption')
                        <h5 class="row">
                            <div class="col-8">{{ __('Caption') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('Remove') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="caption">
                        <input type="text" class="form-control" name="sections[{{ $index }}][caption]" value="{{ $section['caption'] }}">
                        @break
                    @case('text')
                        <h5 class="row">
                            <div class="col-8">{{ __('Text') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('Remove') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="text">
                        <textarea class="form-control" name="sections[{{ $index }}][text]" rows="8">{!! e($section['text']) !!}</textarea>
                        @break
                    @case('image')
                        <h5 class="row">
                            <div class="col-8">{{ __('Image') }}</div>
                            <div class="col-4 ml-auto text-right">
                                <button class="btn btn-sm btn-danger js-remove-section">{{ __('Remove') }}</button>
                            </div>
                        </h5>
                        <input type="hidden" name="sections[{{ $index }}][type]" value="image">
                        <div class="mb-2">
                            <img id="image_preview_{{ $index }}" class="preview img-thumbnail " src="{{ old("sections.{$index}.image_preview_url",
                                    isset($section['id']) ? $article->getImageUrl($section['id']) : asset('storage/'.config('attachment.no-thumbnail'))) }}">
                            <input type="hidden" id="image_preview_url_{{ $index }}" name="sections[{{ $index }}][image_preview_url]" value="{{ old("sections.{$index}.image_preview_url") }}">
                            <input type="hidden" id="image_id_{{ $index }}" name="sections[{{ $index }}][id]" value="{{ old("sections.{$index}.id", $section['id']) }}">
                        </div>
                        <div>
                            <a href="#" class="btn btn-secondary js-open-uploader"
                                data-input="#image_id_{{ $index }}" data-preview="#image_preview_{{ $index }}" data-preview-url="#image_preview_url_{{ $index }}" data-only-image="true"
                            >{{ __('Open File Manager') }}</a>
                        </div>
                        @break
                @endswitch
                <hr>
            </div>
        @empty
            <div class="form-group js-section" data-section="0">
                <h5 class="row">
                    <div class="col-8">{{ __('Text') }}</div>
                    <div class="col-4 ml-auto text-right">
                        <button class="btn btn-sm btn-danger js-remove-section">{{ __('Remove') }}</button>
                    </div>
                </h5>
                <input type="hidden" name="sections[0][type]" value="text">
                <textarea class="form-control" name="sections[0][text]" rows="8"></textarea>
            </div>
        @endforelse
    </div>
    <a href="#" class="btn btn-secondary js-add-caption-section">{{ __('Add Caption') }}</a>
    <a href="#" class="btn btn-secondary js-add-text-section">{{ __('Add Text') }}</a>
    <a href="#" class="btn btn-secondary js-add-image-section">{{ __('Add Image') }}</a>

</div>
<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('Optional') }}</span>{{ __('Categories') }}</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'page', 'categories' => $categories->get('page')])
    </div>
</div>
