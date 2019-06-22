@extends('layouts.app')

@section('title', __('message.mypage'))

@section('content')
    <p class="mb-4">
        @foreach (config('category.post') as $post_type)
            <a class="btn btn-primary" href="{{ route('mypage.articles.create', $post_type['slug']) }}">
                {{ __('message.create-article-of', ['type' => __('category.post.'.$post_type['slug'])]) }}</a>
        @endforeach
    </p>

    <h2>{{ __('message.my-articles') }}</h2>

    @if ($articles->isEmpty())
       <p>{{ __('message.no-article') }}</p>
    @else
        <table class="table table-bordered">
            <thead>
                <th>{{ __('article.id') }}</th>
                <th>{{ __('article.status') }}</th>
                <th>{{ __('article.title') }}</th>
                <th>{{ __('category.type.post') }}</th>
                <th>{{ __('article.page-view') }}</th>
                <th>{{ __('article.conversion') }}</small></th>
                <th>{{ __('article.conversion-rate') }}</th>
                <th>{{ __('article.actions') }}</th>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>{{ $article->id }}</td>
                        <td>{{ __('status.'.$article->status) }}</td>
                        <td>{{ $article->title }}</td>
                        <td>{{ __('category.post.'.$article->category_post->slug) }}</td>
                        <td>{{ $article->views_count }}</td>
                        <td>{{ $article->conversions_count }}</td>
                        <td>{{ $article->conversion_rate }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('mypage.articles.edit', $article) }}">{{ __('message.edit') }}</a>
                            @if ($article->is_publish)
                                <a class="btn btn-secondary" href="{{ route('articles.show', $article) }}">{{ __('message.show') }}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
