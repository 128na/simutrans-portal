@csrf

<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">Required</span>Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{ old('title', $article->title ?? '') }}">
</div>
<div class="form-group">
    <label for="slug"><span class="badge badge-secondary mr-1">Optional</span>Slug</label>
    <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="{{ old('title', $article->slug ?? '') }}">
    <small class="form-text text-muted">If empty, use the title.</small>
</div>
<div class="form-group">
    <label><span class="badge badge-secondary mr-1">Optional</span>Thumbnail Image</label>
    <div class="mb-2">
        <img id="thumbnail-preview" class="preview img-thumbnail " src="{{ $article->thumbnail_url ?? '' }}">
    </div>
    <div class="custom-file">
        <label class="custom-file-label" for="thumbnail">{{ old('title', $article->thumbnail->original_name ?? 'Choose file') }}</label>
        <input type="file" class="custom-file-input js-preview-trigger" id="thumbnail" name="thumbnail" data-preview="#thumbnail-preview">
    </div>
</div>
<div class="form-group">
    <label for="title"><span class="badge badge-danger mr-1">Required</span>Author</label>
    <input type="text" class="form-control" id="author" name="author" placeholder="Author" value="{{ old('author', $article->author ?? '') }}">
</div>
<div class="form-group">
    <label for="description"><span class="badge badge-danger mr-1">Required</span>Decription</label>
    <textarea class="form-control" id="description" name="description" rows="10">{{ old('title', $article->description ?? '') }}</textarea>
</div>

<div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Check me out</label>
</div>
<button type="submit" class="btn btn-lg btn-primary">Submit</button>

<script src="{{ asset('js/form.js') }}" defer></script>
