@extends('layouts.mypage')

@section('title', __('Mypage'))

@section('content')
    <script src="{{ asset(mix('js/mypage.js')) }}" defer></script>
    <div id="app"></div>
    {{-- <div class="mypage">

        <h2>@lang('Profile')</h2>

        @include('parts.profile-card', ['in_mypage' => true])

        <p>
            <a class="btn btn-outline-primary" href="{{route('mypage.articles.analytics') }}">@lang('Access Analytics')</a>
        </p>
        <p class="mb-4">
            <a class="btn btn-outline-primary" href="{{ route('mypage.articles.create') }}">
                @lang('Create New Article')</a>
        </p>

        <h2>@lang('My articles')</h2>
        @if ($articles->isEmpty())
        <p>@lang('No article exists.')</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <th>@lang('ID')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Title')</th>
                    <th>@lang('Post Type')</th>
                    <th>@lang('Created at') / @lang('Updated at')</th>
                    <th>@lang('Conversion Rate')</th>
                    <th>@lang('Actions')</th>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr class="status-{{ $article->status }}">
                            <td>{{ $article->id }}</td>
                            <td>@lang('statuses.'.$article->status)</td>
                            <td>{{ $article->title }}</td>
                            <td>@lang('post_types.'.$article->post_type)</td>
                            <td>{{$article->created_at->formatLocalized(__('%m-%d-%Y %k:%M:%S')) }}<br>{{$article->updated_at->formatLocalized(__('%m-%d-%Y %k:%M:%S')) }}</td>
                            <td>{{ $article->todaysConversionCount->count ?? 0 }} / {{ $article->todaysViewCount->count ?? 0 }} = {{ $article->todays_conversion_rate }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('mypage.articles.edit', $article) }}">@lang('Edit')</a>
                                    @if ($article->is_publish)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('articles.show', $article->slug) }}" target="_blank">@lang('Show')</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div> --}}
@endsection
