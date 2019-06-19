@extends('layouts.app')

@section('title', 'MyPage')

@section('content')
    @foreach (config('category.post') as $post_type)
        <a class="btn btn-primary" href="{{ route('mypage.articles.create', $post_type['slug']) }}">New {{ $post_type['name'] }}</a>
    @endforeach

    <h2>Articles</h2>

    <table class="table table-bordered">
        <thead>
            <th>ID</th>
            <th>Status</th>
            <th>Title</th>
            <th>Type</th>
            <th>Views</th>
            <th>Conversion</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($user->articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>{{ $article->status }}</td>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->category_post->name }}</td>
                    <td>0</td>
                    <td>0</td>
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
@endsection
