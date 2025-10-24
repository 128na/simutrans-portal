@foreach($article->contents->sections as $section)
@switch($section->type)
@case('caption')
<h3 class="text-xl font-semibold sm:text-xl my-8">{{ $section->caption }}</h3>
@break

@case('text')
<pre class="whitespace-pre-wrap text-gray-800">{{ $section->text }}</pre>
@break

@case('url')
@include('v2.parts.link-external', ['url' => $section->url, 'title' => $section->url])
@break

@case('image')
<img src="{{ $article->getAttachment($section->id)?->thumbnail }}" alt="" class="mt-6 mb-12 w-max-full rounded-lg shadow-md">
@break

@endswitch
@endforeach
