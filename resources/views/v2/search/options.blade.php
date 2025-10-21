<form method="GET" action="{{ route('search') }}">
    @csrf
    <div class="flex flex-col gap-y-4 sm:flex-row sm:items-center sm:gap-x-4">
        <input type="text" name="word" value="{{ $condition['word'] ?? '' }}" placeholder="キーワードを入力" class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500 sm:w-64" />
        <button type="submit" class="rounded-md bg-brand px-4 py-2 text-white cursor-pointer">
            検索
        </button>
    </div>
    <div>
        <button type="button" command="--toggle" commandfor="search-options" class="my-2 p-2 flex w-full items-center justify-between bg-gray-100 cursor-pointer">
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
                <div class="font-bold ">投稿形式</div>
                @foreach($options['postTypes'] as $postType)
                <label class="mr-2 inline-block cursor-pointer">
                    <input class="accent-brand" type="checkbox" name="postTypes[]" value="{{ $postType->value }}" {{ in_array($postType->value, $condition['postTypes'] ?? []) ? 'checked' : '' }} />
                    @lang("post_types.{$postType->value}")
                </label>
                @endforeach
            </div>

            @foreach($options['categories']->groupBy('type') as $type => $categories)
            <div class="mb-4">
                <div class="font-bold ">@lang("category.type.{$type}")</div>
                @foreach($categories as $category)
                <label class="mr-2 inline-block cursor-pointer">
                    <input class="accent-brand" type="checkbox" name="categoryIds[]" value="{{ $category->id }}" {{ in_array($category->id, $condition['categoryIds'] ?? []) ? 'checked' : '' }} />
                    @lang("category.{$type}.{$category->slug}")
                </label>
                @endforeach
            </div>
            @endforeach

            <button type="button" command="--toggle" commandfor="search-users" class="my-2 p-2 flex w-full items-center justify-between bg-gray-100 cursor-pointer">
                ユーザー
                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
                    <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                </svg>
            </button>
            <el-disclosure id="search-users" {{ $condition['userIds'] ?? [] ? '' : 'hidden' }} class="mb-4 block space-y-2 gap-y-2">
                <div id="app-search-users" data-user-ids='@json($condition["userIds"] ?? [])' data-options='@json($options["users"] ?? [])'>Loading...</div>
            </el-disclosure>

            <button type="button" command="--toggle" commandfor="search-tags" class="my-2 p-2 flex w-full items-center justify-between bg-gray-100 cursor-pointer">
                タグ
                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none in-aria-expanded:rotate-180">
                    <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                </svg>
            </button>
            <el-disclosure id="search-tags" {{ $condition['tagIds'] ?? [] ? '' : 'hidden' }} class="mb-4 block space-y-2 gap-y-2">
                <div id="app-search-tags" data-tag-ids='@json($condition["tagIds"] ?? [])' data-options='@json($options["tags"] ?? [])'>Loading...</div>
            </el-disclosure>
        </el-disclosure>
    </div>
</form>
