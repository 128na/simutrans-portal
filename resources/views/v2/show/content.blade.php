<div class="mx-auto max-w-7xl p-6 lg:px-8">
    <h2 class="text-4xl font-semibold text-pretty text-gray-900 sm:text-5xl mb-8">{{$article->title}}</h2>
    <div class="text-sm">
        @foreach($article->categories as $category)
        <a href="{{ route('search', ['categoryIds' => [$category->id]]) }}" class="rounded bg-category px-2 py-1 text-white">@lang("category.{$category->type->value}.{$category->slug}")</a>
        @endforeach
        @foreach($article->tags as $tag)
        <a href="{{ route('search', ['tagIds' => [$tag->id]]) }}" class="rounded bg-tag px-2 py-1 text-white">{{$tag->name}}</a>
        @endforeach
    </div>
    <img src="{{ $article->thumbnailUrl }}" alt="" class="mt-6 w-max-full rounded-lg shadow-md">
    <div></div>
</div>
