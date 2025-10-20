@foreach($categories as $category)
<a href="{{ route('search', ['categoryIds' => [$category->id]]) }}" class="rounded bg-category px-2.5 py-0.5 text-white mb-1 mr-1 inline-block">@lang("category.{$category->type->value}.{$category->slug}")</a>
@endforeach
