@extends('layouts.app')

@section('title', __('message.mypage'))

@section('content')

    <h2>{{ __('message.profile') }}</h2>
    <div class="card mb-3 card-profile">
        <div class="row no-gutters overflow-hidden">
            <div class="col-md-4">
                <a href="{{ route('mypage.profile.edit') }}">
                    <img src="{{ $user->profile->avatar_url }}" class="card-img img-fluid" alt="...">
                </a>
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <div class="card-title">
                        @if ($user->email_verified_at)
                            <span class="rounded border border-success text-success">{{ __('message.email-verified') }}</span>
                        @else
                            <span class="rounded border border-danger text-danger">{{ __('message.email-not-verified') }}</span>
                        @endif
                        <h5 class="mt-2">{{ $user->name }}</h5>
                    </div>
                    <p class="card-text">{{ $user->profile->getContents('description') ?? __('message.no-description') }}</p>
                    <p class="card-text">
                        <a href="{{ route('mypage.profile.edit') }}">
                            {{ __('message.edit-profile') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

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
                        <td>{{ $article->conversions_count }} / {{ $article->views_count }} = {{ $article->conversion_rate }}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('mypage.articles.edit', $article) }}">{{ __('message.edit') }}</a>
                                @if ($article->is_publish)
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('articles.show', $article) }}">{{ __('message.show') }}</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
