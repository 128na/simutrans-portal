<form method="POST" action="{{ route('advancedSearch') }}">
    @csrf
    <div class="card mt-2">
        <div class="card-body">
            <h5 class="card-title">検索条件（{{ $articles->total() }}件）</h5>
            <div class="card-text">
                @php
                    $wordSelelected = !empty($advancedSearch['word']);
                @endphp
                <div data-toggle="collapse" data-target="#adv-word" class="clickable mb-2"
                    aria-expanded="{{ $wordSelelected ? 'true' : 'false' }}" aria-controls="adv-word">
                    ▼キーワード
                </div>

                <div class="collapse ml-3 {{ $wordSelelected ? 'show' : '' }}" id="adv-word">
                    <div class="form-group">
                        <input type="text" class="form-control" id="word" name="advancedSearch[word]"
                            value="{{ old('advancedSearch.word', $advancedSearch['word'] ?? null) }}">
                        <small class="form-text text-muted">AND検索はスペース区切り（国鉄 有蓋車）、OR検索はORで区切ります（都営地下鉄 OR 東京メトロ）</small>
                    </div>
                </div>

                @php
                    $updatedAtSelelected = !empty($advancedSearch['startAt']) || !empty($advancedSearch['endAt']);
                @endphp
                <div data-toggle="collapse" data-target="#adv-updated-at" class="clickable mb-2"
                    aria-expanded="{{ $updatedAtSelelected ? 'true' : 'false' }}" aria-controls="adv-updated-at">
                    ▼更新期間
                </div>
                <div class="collapse ml-3 {{ $updatedAtSelelected ? 'show' : '' }}" id="adv-updated-at">

                    <div class="form-group">
                        <div class="input-group">
                            <input type="date" class="form-control" name="advancedSearch[startAt]" id="start"
                                value="{{ old('advancedSearch.startAt', $advancedSearch['startAt'] ?? null) }}">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text">～</span>
                            </div>

                            <input type="date" class="form-control" name="advancedSearch[endAt]"
                                value="{{ old('advancedSearch.endAt', $advancedSearch['endAt'] ?? null) }}">
                        </div>
                    </div>
                </div>

                @php
                    $categorySelelected = !empty($advancedSearch['categoryIds']);
                @endphp
                <div data-toggle="collapse" data-target="#adv-category" class="clickable mb-2"
                    aria-expanded="{{ $categorySelelected ? 'true' : 'false' }}" aria-controls="adv-category">
                    ▼カテゴリ
                </div>
                <div class="collapse ml-3 {{ $categorySelelected ? 'show' : '' }}" id="adv-category">
                    <div class="form-group">
                        <label for="category-and">カテゴリ条件</label>
                        <select id="category-and" class="form-control" name="advancedSearch[categoryAnd]">
                            <option value=1
                                {{ old('advancedSearch.categoryAnd', $advancedSearch['categoryAnd'] ?? true) ? 'selected' : '' }}>
                                全てを含む
                            </option>
                            <option value=0
                                {{ old('advancedSearch.categoryAnd', $advancedSearch['categoryAnd'] ?? true) ? '' : 'selected' }}>
                                いずれかを含む
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="d-block">@lang('category.type.pak')</label>
                        @foreach ($options['categories']->filter(fn($c) => $c->type === 'pak') as $c)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="cat_{{ $c->id }}"
                                    name="advancedSearch[categoryIds][{{ $c->id }}]"
                                    value="{{ $c->id }}"
                                    {{ in_array($c->id, old('advancedSearch.categoryIds', $advancedSearch['categoryIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="cat_{{ $c->id }}">
                                    @lang("category.pak.$c->slug")
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label class="d-block">@lang('category.type.addon')</label>
                        @foreach ($options['categories']->filter(fn($c) => $c->type === 'addon') as $c)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="cat_{{ $c->id }}"
                                    name="advancedSearch[categoryIds][{{ $c->id }}]"
                                    value="{{ $c->id }}"
                                    {{ in_array($c->id, old('advancedSearch.categoryIds', $advancedSearch['categoryIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="cat_{{ $c->id }}">
                                    @lang("category.addon.$c->slug")
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label class="d-block">@lang('category.type.pak128_position')</label>
                        @foreach ($options['categories']->filter(fn($c) => $c->type === 'pak128_position') as $c)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="cat_{{ $c->id }}"
                                    name="advancedSearch[categoryIds][{{ $c->id }}]"
                                    value="{{ $c->id }}"
                                    {{ in_array($c->id, old('advancedSearch.categoryIds', $advancedSearch['categoryIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="cat_{{ $c->id }}">
                                    @lang("category.pak128_position.$c->slug")
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label class="d-block">@lang('category.type.license')</label>
                        @foreach ($options['categories']->filter(fn($c) => $c->type === 'license') as $c)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="cat_{{ $c->id }}"
                                    name="advancedSearch[categoryIds][{{ $c->id }}]"
                                    value="{{ $c->id }}"
                                    {{ in_array($c->id, old('advancedSearch.categoryIds', $advancedSearch['categoryIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="cat_{{ $c->id }}">
                                    @lang("category.license.$c->slug")
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $tagSelelected = !empty($advancedSearch['tagIds']);
                @endphp
                <div data-toggle="collapse" data-target="#adv-tag" class="clickable mb-2"
                    aria-expanded="{{ $tagSelelected ? 'true' : 'false' }}" aria-controls="adv-tag">
                    ▼タグ
                </div>
                <div class="collapse ml-3 {{ $tagSelelected ? 'show' : '' }}" id="adv-tag">
                    <div class="form-group">
                        <label for="tag-and">タグ条件</label>
                        <select id="tag-and" class="form-control" name="advancedSearch[tagAnd]">
                            <option value=1
                                {{ old('advancedSearch.tagAnd', $advancedSearch['tagAnd'] ?? true) ? 'selected' : '' }}>
                                全てを含む
                            </option>
                            <option value=0
                                {{ old('advancedSearch.tagAnd', $advancedSearch['tagAnd'] ?? true) ? '' : 'selected' }}>
                                いずれかを含む
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        @foreach ($options['tags'] as $t)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="tag_{{ $t->id }}"
                                    name="advancedSearch[tagIds][{{ $t->id }}]" value="{{ $t->id }}"
                                    {{ in_array($t->id, old('advancedSearch.tagIds', $advancedSearch['tagIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tag_{{ $t->id }}">
                                    {{ $t->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $userSelelected = !empty($advancedSearch['userIds']);
                @endphp
                <div data-toggle="collapse" data-target="#adv-user" class="clickable mb-2"
                    aria-expanded="{{ $userSelelected ? 'true' : 'false' }}" aria-controls="adv-user">
                    ▼ユーザー
                </div>
                <div class="collapse ml-3 {{ $userSelelected ? 'show' : '' }}" id="adv-user">
                    <div class="form-group">
                        <label for="user-and">ユーザー条件</label>
                        <select id="user-and" class="form-control" name="advancedSearch[userAnd]">
                            <option value=1
                                {{ old('advancedSearch.userAnd', $advancedSearch['userAnd'] ?? true) ? 'selected' : '' }}>
                                全てを含む
                            </option>
                            <option value=0
                                {{ old('advancedSearch.userAnd', $advancedSearch['userAnd'] ?? true) ? '' : 'selected' }}>
                                いずれかを含む
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        @foreach ($options['users'] as $u)
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="user_{{ $u->id }}"
                                    name="advancedSearch[userIds][{{ $u->id }}]" value="{{ $u->id }}"
                                    {{ in_array($u->id, old('advancedSearch.userIds', $advancedSearch['userIds'] ?? [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="user_{{ $u->id }}">
                                    {{ $u->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="order">表示順</label>
                    <div class="input-group">
                        <select class="form-control" name="advancedSearch[order]" id="order">
                            <option value="created_at"
                                {{ old('advancedSearch.order', $advancedSearch['order'] ?? 'created_at') === 'created_at' ? 'selected' : '' }}>
                                投稿日
                            </option>
                            <option value="updated_at"
                                {{ old('advancedSearch.order', $advancedSearch['order'] ?? 'created_at') === 'updated_at' ? 'selected' : '' }}>
                                更新日
                            </option>
                            <option value="title"
                                {{ old('advancedSearch.order', $advancedSearch['order'] ?? 'created_at') === 'title' ? 'selected' : '' }}>
                                タイトル
                            </option>
                        </select>
                        <select class="form-control" name="advancedSearch[direction]">
                            <option value="desc"
                                {{ old('advancedSearch.direction', $advancedSearch['direction'] ?? 'desc') === 'desc' ? 'selected' : '' }}>
                                降順（z→a, 9→0）
                            </option>
                            <option value="asc"
                                {{ old('advancedSearch.direction', $advancedSearch['direction'] ?? 'desc') === 'asc' ? 'selected' : '' }}>
                                昇順（a→z, 0→9）
                            </option>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="form-group text-center mt-4">
                    <button class="btn btn-primary px-5">検索</button>
                </div>
            </div>
        </div>
    </div>
</form>
