@extends('layouts.mypage')

@section('title', __('Edit :title', ['title' => $article->title]))

@section('content')
    @if ($article->contents->isMarkdownContent())
        <form method="POST" action="{{ route('mypage.articles.update.markdown', $article) }}"
            class="js-previewable-form" data-preview-action="{{ route('mypage.articles.update.markdown'.$article, [$article,'preview'], false) }}">
    @else
        <form method="POST" action="{{ route('mypage.articles.update.'.$article->post_type, $article) }}"
            class="js-previewable-form" data-preview-action="{{ route('mypage.articles.update.'.$article, [$article,'preview'], false) }}">
    @endif
        @csrf
        @include('parts._form-common')

        @includeWhen($article->contents->isAddonIntroductionContent(), 'parts._form-addon-introduction')
        @includeWhen($article->contents->isAddonPostContent(), 'parts._form-addon-post')
        @includeWhen($article->contents->isPageContent(), 'parts._form-page')
        @includeWhen($article->contents->isMarkdownContent(), 'parts._form-markdown')

        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">@lang('Save')</button>
            <button class="btn btn btn-secondary js-open-preview">@lang('Preview')</button>
        </div>
    </form>
@endsection
