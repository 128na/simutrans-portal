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
                <th>ID</th>
                <th>Status</th>
                <th>Title</th>
                <th>Type</th>
                <th>PV <small>(Page Views)</small></th>
                <th>CV <small>(Conversion)</small></th>
                <th>CVR <small>(Conversion Rate)</small></th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>{{ $article->id }}</td>
                        <td>{{ $article->status }}</td>
                        <td>{{ $article->title }}</td>
                        <td>{{ $article->category_post->name }}</td>
                        <td>{{ $article->views_count }}</td>
                        <td>{{ $article->conversions_count }}</td>
                        <td>{{ $article->conversion_rate }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('mypage.articles.edit', $article) }}">Edit</a>
                            @if ($article->is_publish)
                                <a class="btn btn-secondary" href="{{ route('articles.show', $article) }}">Show</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
