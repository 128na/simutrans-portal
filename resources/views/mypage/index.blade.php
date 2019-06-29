@extends('layouts.app')

@section('title', __('message.mypage'))

@section('content')
    <div class="mypage">

        <h2>{{ __('message.profile') }}</h2>

        @include('parts.profile-card', ['in_mypage' => true])

        <h2>{{ __('message.my-articles') }}</h2>
        <p class="mb-4">
            @foreach (config('category.post') as $post_type)
                <a class="btn btn-outline-primary" href="{{ route('mypage.articles.create', $post_type['slug']) }}">
                    {{ __('message.create-article-of', ['type' => __('category.post.'.$post_type['slug'])]) }}</a>
            @endforeach
        </p>

        @if ($articles->isEmpty())
        <p>{{ __('message.no-article') }}</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <th>{{ __('article.id') }}</th>
                    <th>{{ __('article.status') }}</th>
                    <th>{{ __('article.title') }}</th>
                    <th>{{ __('category.type.post') }}</th>
                    <th>{{ __('article.created_at') }} / {{ __('article.updated_at') }}</th>
                    <th>{{ __('article.conversion-rate') }}</th>
                    <th>{{ __('article.actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr>
                            <td>{{ $article->id }}</td>
                            <td>{{ __('status.'.$article->status) }}</td>
                            <td>{{ $article->title }}</td>
                            <td>{{ __('category.post.'.$article->post_type) }}</td>
                            <td>{{$article->created_at }}<br>{{$article->updated_at }}</td>
                            <td>{{ $article->todaysConversionCount->count ?? 0 }} / {{ $article->todaysViewCount->count ?? 0 }} = {{ $article->todays_conversion_rate }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('mypage.articles.edit', $article) }}">{{ __('message.edit') }}</a>
                                    @if ($article->is_publish)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('articles.show', $article) }}" target="_blank" rel="noopener">{{ __('message.show') }}</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
