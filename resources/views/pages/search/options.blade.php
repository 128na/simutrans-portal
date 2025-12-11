<form method="GET" action="{{ route('search') }}">
    <div class="flex flex-col gap-y-4 sm:flex-row sm:items-center sm:gap-x-4 mb-4">
        <input type="search" name="word" value="{{ $condition['word'] ?? '' }}" placeholder="キーワードを入力" class="w-full sm:w-64 v2-input" />
        <button type="submit" class="v2-button v2-button-lg v2-button-primary">
            検索
        </button>
    </div>
    <script id="data-options" type="application/json">
        @json($options)

    </script>
    <div class="mb-12">
        <button type="button" command="--toggle" commandfor="search-options" class="v2-accordion">
            詳細条件
            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
            </svg>
        </button>
        @php
        $showOptions = ($condition['tagIds'] ?? []) || ($condition['categoryIds'] ?? []) || ($condition['userIds'] ?? []) || ($condition['postTypes'] ?? []);
        @endphp
        <el-disclosure id="search-options" {{ $showOptions ? '' : 'hidden' }} class="mt-2 block space-y-2">
            <div class="mb-4">
                <div class="font-semibold">投稿形式</div>
                <div class="v2-checkboxes">
                    @foreach($options['postTypes'] as $postType)
                    <label>
                        <input class="v2-checkbox peer" type="checkbox" name="postTypes[]" value="{{ $postType->value }}" {{ in_array($postType->value, $condition['postTypes'] ?? []) ? 'checked' : '' }} />
                        <span class="v2-checkbox-label">
                            @lang("post_types.{$postType->value}")
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            @foreach($options['categories']->groupBy('type') as $type => $categories)
            <div class="mb-4">
                <div class="font-semibold ">@lang("category.type.{$type}")</div>
                <div class="v2-checkboxes">
                    @foreach($categories as $category)
                    <label>
                        <input class="v2-checkbox peer" type="checkbox" name="categoryIds[]" value="{{ $category->id }}" {{ in_array($category->id, $condition['categoryIds'] ?? []) ? 'checked' : '' }} />
                        <span class="v2-checkbox-label">
                            @lang("category.{$type}.{$category->slug}")
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <button type="button" command="--toggle" commandfor="search-users" class="v2-accordion">
                ユーザー
                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
                    <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                </svg>
            </button>
            <el-disclosure id="search-users" {{ $condition['userIds'] ?? [] ? '' : 'hidden' }} class="mb-4 block space-y-2 gap-y-2">
                <div id="app-search-users" data-user-ids='@json($condition["userIds"] ?? [])'>Loading...</div>
            </el-disclosure>

            <button type="button" command="--toggle" commandfor="search-tags" class="v2-accordion">
                タグ
                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
                    <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                </svg>
            </button>
            <el-disclosure id="search-tags" {{ $condition['tagIds'] ?? [] ? '' : 'hidden' }} class="mb-4 block space-y-2 gap-y-2">
                <div id="app-search-tags" data-tag-ids='@json($condition["tagIds"] ?? [])'>Loading...</div>
            </el-disclosure>
        </el-disclosure>
    </div>
</form>
