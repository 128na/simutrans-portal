<div class="form-group">
    <label for="data"><span class="badge badge-danger mr-1">@lang('Required')</span>@lang('Contents')</label>
    <textarea class="form-control" id="data" name="data" rows="4">{!! e(old('data', $article->contents->data ?? '')) !!}</textarea>
</div>
<div class="form-group">
    <label><span class="badge badge-secondary mr-1">@lang('Optional')</span>@lang('Categories')</label>
    <div class="category-list">
        @include('parts._form-category-list', ['name' => 'page', 'categories' => $categories->get('page')])
    </div>
</div>
