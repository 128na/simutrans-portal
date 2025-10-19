<article class="flex max-w-xl flex-col items-start justify-between">
    <time datetime="{{$article->published_at->format('Y/m/d')}}" class="text-sm mb-1 text-gray-500">{{$article->published_at->format('Y/m/d')}}</time>
    <a href="{{ route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user->id, 'articleSlug' => $article->slug]) }}">
        <img class="mb-6 rounded-lg shadow-xl" src="{{ $article->thumbnailUrl }}" />
    </a>
    <div class="group relative ">
        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
            <a href="{{ route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user->id, 'articleSlug' => $article->slug]) }}">
                {{$article->title}}
            </a>
        </h3>
        <p class="mt-5 line-clamp-3 text-sm/6 text-gray-600 break-all">{{$article->metaDescription}}</p>
    </div>
    <div class="text-xs mt-2 flex flex-wrap gap-2">
        @foreach($article->categories as $category)
        <a href="{{ route('search', ['categoryIds' => [$category->id]]) }}" class="rounded-full bg-category px-3 py-1.5 text-white">@lang("category.{$category->type->value}.{$category->slug}")</a>
        @endforeach
        @foreach($article->tags as $tag)
        <a href="{{ route('search', ['tagIds' => [$tag->id]]) }}" class="rounded-full bg-tag px-3 py-1.5 text-white">{{$tag->name}}</a>
        @endforeach
    </div>

    <div class="mt-8 flex items-end gap-x-4 grow justify-self-end">
        <img src="{{$article->user->profile->avatarUrl}}" alt="user's avatar" class="size-10 rounded-full bg-gray-50" />
        <div class="text-sm/6">
            <p class="font-semibold text-gray-900 break-all">
                <a href="{{ route('search', ['userIds' => [$article->user_id]]) }}">
                    {{$article->user->name}}
                </a>
            </p>
            <p class="text-gray-600 break-all">{{$article->user->profile->data->description}}</p>
        </div>
    </div>
</article>
