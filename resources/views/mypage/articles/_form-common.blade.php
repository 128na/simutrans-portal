{{-- 共通フォーム --}}
<div class="form-group">
    <label for="status"><span class="badge badge-danger mr-1">Required</span>Status</label>
    <select class="form-control" id="status" name="status">
        @foreach (config('status', []) as $key => $name)
            <option value="{{ $key }}" {{ old('status', $article->status ?? config('status.draft')) === $key ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">Required</span>Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{ old('title', $article->title ?? '') }}">
</div>

<div class="form-group">
    <label for="slug"><span class="badge badge-secondary mr-1">Optional</span>Slug</label>
    <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="{{ old('slug', $article->slug ?? '') }}">
    <small class="form-text text-muted">If empty, use the title.</small>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Thumbnail Image</label>
    <div class="mb-2">
        <img id="thumbnail-preview" class="preview img-thumbnail " src="{{ $article->thumbnail_url ?? asset('storage/'.config('attachment.no-thumbnail')) }}">
    </div>
    <div class="custom-file">
        <label class="custom-file-label" for="thumbnail">{{ old('thumbnail', $article->thumbnail->original_name ?? 'Choose image file') }}</label>
        <input type="file" class="custom-file-input js-preview-trigger" id="thumbnail" name="thumbnail" data-preview="#thumbnail-preview">
    </div>
</div>
