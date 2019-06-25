
<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.pak') }}</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'pak', 'categories' => $categories->get('pak')])
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.addon-type') }}</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'addon', 'categories' => $categories->get('addon')])
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.pak128-position') }}</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'pak128_position', 'categories' => $categories->get('pak128_position')])
    </div>
</div>

<div class="form-group">
    <label for="tag"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.tag') }}</label>
    <div class="tag-list mb-2">
        @foreach ($article->tags ?? [] as $tag)
            <div class="badge badge-secondary fade show">
                <span class="mr-1">{{ $tag->name }}</span>
                <span class="badge badge-pill badge-light clickable js-remove-tag">&times;</span>
                <input type="hidden" name="tags[]" value="{{ $tag->name }}">
            </div>
        @endforeach
    </div>
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="tag" id="new-tag" autocomplete="off">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary js-add-tag" type="button">{{ __('message.add') }}</button>
        </div>
    </div>

    <a class="btn btn-secondary mb-2" data-toggle="collapse" href="#popular-tags" role="button" aria-expanded="false" aria-controls="popular-tags">
        {{ __('message.select-from-popular') }}
    </a>
    <div class="collapse" id="popular-tags">
        <div class="card card-body">
            @foreach ($popular_tags as $tag)
            <span class="mr-2">
                <a href="#" class="js-add-popular-tag" data-name="{{ $tag->name }}">{{ $tag->name }}</a>
            </span>
            @endforeach
        </div>
    </div>
</div>

<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.license') }}</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'license', 'categories' => $categories->get('license')])
    </div>
</div>

<div class="form-group">
    <label for="license"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('article.license-other') }}</label>
    <textarea class="form-control" id="license" name="license" rows="4">{!! e(old('license', $article->license ?? '')) !!}</textarea>
</div>
