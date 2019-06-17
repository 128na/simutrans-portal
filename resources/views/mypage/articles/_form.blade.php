@csrf

{{-- 投稿タイプ切り替えタブ --}}
@php
    $selected_id = isset($article) ?: old('post', $post_categories->first()->id ?? null);
@endphp
<ul class="nav nav-tabs mb-2" id="myTab" role="tablist">
    @forelse ($post_categories ?? [] as $category_post)
        <li class="nav-item">
            <a class="nav-link {{ $selected_id === $category_post->id ? 'active' : ''}}" id="{{ $category_post->slug }}-tab" data-toggle="tab" href="#{{ $category_post->slug }}"
                role="tab" aria-controls="{{ $category_post->slug }}" aria-selected="true">{{ $category_post->name }}</a>
        </li>
    @empty
        <li class="nav-item">
            <a class="nav-link active" id="selected-tab" data-toggle="tab" href="#selected"
                role="tab" aria-controls="selected" aria-selected="true">{{ $article->category_post->name }}</a>
        </li>
    @endforelse
</ul>

{{-- 共通フォーム --}}
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
        <img id="thumbnail-preview" class="preview img-thumbnail " src="{{ $article->thumbnail_url ?? asset('uploads/no-image.png') }}">
    </div>
    <div class="custom-file">
        <label class="custom-file-label" for="thumbnail">{{ old('title', $article->thumbnail->original_name ?? 'Choose image file') }}</label>
        <input type="file" class="custom-file-input js-preview-trigger" id="thumbnail" name="thumbnail" data-preview="#thumbnail-preview">
    </div>
</div>

{{-- 投稿タイプ別フォーム --}}
<div class="tab-content">
    @forelse ($post_categories ?? [] as $category_post)
        <div class="tab-pane fade {{ $selected_id === $category_post->id ? 'show active' : ''}}" id="{{ $category_post->slug }}"
            role="tabpanel" aria-labelledby="{{ $category_post->slug }}-tab">
            @include('mypage.articles._form-'.$category_post->slug)
        </div>
    @empty
        <div class="tab-pane fade show active" id="selected" role="tabpanel" aria-labelledby="selected-tab">
            @include('mypage.articles._form-'.$article->category_post->slug)
        </div>
    @endforelse
</div>


<div class="form-group">
    <label for="status"><span class="badge badge-danger mr-1">Required</span>Status</label>
    <select class="form-control" id="status" name="status">
        @foreach (config('status', []) as $key => $name)
            <option value="{{ $key }}" {{ old('status', $article->status ?? config('status.draft')) === $key ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
</div>
<button type="submit" class="btn btn-lg btn-primary">Submit</button>

<script src="{{ asset('js/form.js') }}" defer></script>
