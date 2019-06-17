@extends('layouts.app')

@section('title', 'MyPage')

@section('content')
    <p><a class="btn btn-primary" href="{{ route('mypage.articles.create') }}">New Artcile</a></p>

    <h1>Articles</h1>

    <table class="table table-bordered">
        <thead>
            <th>ID</th>
            <th>Type</th>
            <th>Title</th>
            <th>Status</th>
            <th>Views</th>
            <th>Conversion</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($user->articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>{{ $article->category_post->name }}</td>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->status }}</td>
                    <td>0</td>
                    <td>0</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('mypage.articles.edit', $article) }}">Edit</a>
                        <a class="btn btn-secondary" href="{{ route('articles', $article) }}">Show</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
