<p class="mt-3">
    <a class="mr-1" href="{{ route('articles.show', ['userIdOrNickname' => $article->user_nickname ?? $article->user_id, 'articleSlug' => $article->slug]) }}" class="hover:underline">
        {{$article->published_at->format('Y/m/d')}}
        {{$article->title}}
    </a>
    @foreach($article->categories->filter(fn(App\Models\Category $category) => $category->type === App\Enums\CategoryType::Pak) as $category)
    <span class="inline-flex items-center rounded-md bg-sky-50 px-2 py-1 text-xs font-medium text-sky-600 inset-ring inset-ring-sky-500/10">
        {{$category->slug}}
    </span>
    @endforeach
</p>
