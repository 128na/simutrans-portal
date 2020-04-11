<h3>
    <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
</h3>
<p>
    @lang('We have detected that the download destination URL of ":title" is broken.', ['title' => $article->title])
</p>
<p>
    @lang('The article was automatically changed to private. If the link destination changes, please correct the article.')
</p>
<p>
    @lang('Mypage:')
    <a href="{{ route('mypage.index') }}">{{ route('mypage.index') }}</a>
</p>
