@extends('layouts.mypage')

@section('title', __('Mypage'))

@section('content')
    <div class="mypage">

        <h2>{{ __('Profile') }}</h2>

        @include('parts.profile-card', ['in_mypage' => true])

        <p>
            <a class="btn btn-outline-primary" href="{{route('mypage.articles.analytics') }}">{{ __('Access Analytics') }}</a>
        </p>
        <p class="mb-4">
            @foreach (config('category.post') as $post_type)
                <a class="btn btn-outline-primary" href="{{ route('mypage.articles.create', $post_type['slug']) }}">
                    {{ __('Create :post_type', ['post_type' => __('post_types.'.$post_type['slug'])]) }}</a>
            @endforeach
        </p>

        <h2>{{ __('My articles') }}</h2>
        @if ($articles->isEmpty())
        <p>{{ __('No article exists.') }}</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Post Type') }}</th>
                    <th>{{ __('Created at') }} / {{ __('Updated at') }}</th>
                    <th>{{ __('Conversion Rate') }}</th>
                    <th>{{ __('Actions') }}</th>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr class="status-{{ $article->status }}">
                            <td>{{ $article->id }}</td>
                            <td>{{ __('statuses.'.$article->status) }}</td>
                            <td>{{ $article->title }}</td>
                            <td>{{ __('post_types.'.$article->post_type) }}</td>
                            <td>{{$article->created_at }}<br>{{$article->updated_at }}</td>
                            <td>{{ $article->todaysConversionCount->count ?? 0 }} / {{ $article->todaysViewCount->count ?? 0 }} = {{ $article->todays_conversion_rate }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('mypage.articles.edit', $article) }}">{{ __('Edit') }}</a>
                                    @if ($article->is_publish)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('articles.show', $article->slug) }}" target="_blank">{{ __('Show') }}</a>
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
